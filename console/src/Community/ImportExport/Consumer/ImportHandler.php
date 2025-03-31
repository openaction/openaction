<?php

namespace App\Community\ImportExport\Consumer;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Entity\Community\Contact;
use App\Entity\Community\Import;
use App\Repository\Community\ImportRepository;
use App\Repository\Platform\JobRepository;
use App\Search\CrmIndexer;
use App\Util\Address;
use App\Util\PhoneNumber;
use App\Util\Spreadsheet;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use League\Flysystem\FilesystemReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use function Symfony\Component\String\u;

use Symfony\Component\String\UnicodeString;

/**
 * Process import files.
 */
#[AsMessageHandler]
final class ImportHandler
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly ImportRepository $importRepository,
        private readonly JobRepository $jobRepository,
        private readonly FilesystemReader $cdnStorage,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
        private readonly CrmIndexer $crmIndexer,
    ) {
    }

    public function __invoke(ImportMessage $message)
    {
        if (!$import = $this->importRepository->find($message->getImportId())) {
            $this->logger->error('Import not found by its ID', ['id' => $message->getImportId()]);

            return true;
        }

        if ($import->getJob()->isFinished()) {
            $this->logger->error('Import finished', ['id' => $message->getImportId()]);

            return true;
        }

        if (!$organization = $import->getOrganization()) {
            $this->logger->error('Invalid organization', ['id' => $message->getImportId()]);

            return true;
        }

        $jobId = $import->getJob()->getId();

        $db = $this->em->getConnection();

        /*
         * Download file locally
         */
        $this->jobRepository->setJobStep($jobId, step: 1, payload: ['status' => 'downloading_file']);
        $localFile = $this->downloadImportFile($import);

        /*
         * Prepare database
         */
        $this->jobRepository->setJobStep($jobId, step: 2, payload: ['status' => 'preparing_database']);

        $columnsSlugged = [
            'profileFirstName',
            'profileMiddleName',
            'profileLastName',
            'profileCompany',
            'profileJobTitle',
            'addressStreetLine1',
            'addressStreetLine2',
        ];

        $columnsMapping = [];
        foreach ($import->getHead()->getMatchedColumns() as $key => $column) {
            if ('ignored' !== $column && !isset($columnsMapping[$column])) {
                $columnsMapping[$column] = $key;

                if (\in_array($column, $columnsSlugged, true)) {
                    $columnsMapping[$column.'Slug'] = $key;
                } elseif ('contactPhone' === $column) {
                    $columnsMapping['parsedContactPhone'] = $key;
                } elseif ('contactWorkPhone' === $column) {
                    $columnsMapping['parsedContactWorkPhone'] = $key;
                }
            }
        }

        // Remove data import table if it already exists
        $tableName = 'community_imports_data_'.$import->getId();
        $db->executeStatement('DROP TABLE IF EXISTS '.$tableName);

        // Create data import table
        $table = new Table($tableName);
        foreach (array_keys($columnsMapping) as $column) {
            $table->addColumn(strtolower($column), 'text', ['notnull' => false]);
        }

        foreach ($db->getDatabasePlatform()->getCreateTableSQL($table) as $sql) {
            $db->executeStatement($sql);
        }

        /*
         * Prepare SQL for raw import
         */
        $this->jobRepository->setJobStep($jobId, step: 3, payload: ['status' => 'parsing_file']);

        $sqlValues = [];
        $isHead = true;

        foreach (Spreadsheet::open($localFile) as $row) {
            // Ignore head
            if ($isHead) {
                $isHead = false;
                continue;
            }

            $country = null;
            if (isset($columnsMapping['addressCountry'])
                && !empty($row[$columnsMapping['addressCountry']])
                && Countries::exists(strtoupper($row[$columnsMapping['addressCountry']]))) {
                $country = strtoupper($row[$columnsMapping['addressCountry']]);
            }

            $rowValues = [];
            foreach ($columnsMapping as $type => $key) {
                if (empty($row[$key])) {
                    $rowValues[] = 'null';

                    continue;
                }

                if (null === $normalized = $this->normalizeValue($type, $row[$key], $country)) {
                    $rowValues[] = 'null';

                    continue;
                }

                $rowValues[] = $db->quote($normalized);
            }

            $sqlValues[] = '('.implode(',', $rowValues).')';
        }

        /*
         * Raw import
         */
        $progress = 0;
        $this->jobRepository->setJobStep($jobId, step: 4, payload: ['status' => 'importing_raw_data', 'progress' => $progress]);

        foreach (array_chunk($sqlValues, 10_000) as $chunk) {
            $columnsNames = [];
            foreach (array_keys($columnsMapping) as $column) {
                $columnsNames[] = strtolower($column);
            }

            $db->executeStatement(
                'INSERT INTO '.$tableName.' ('.implode(',', $columnsNames).') '.
                'VALUES '.implode(',', $sqlValues)
            );

            // Update status
            $progress += count($chunk);
            $this->jobRepository->setJobStep($jobId, step: 4, payload: ['status' => 'importing_raw_data', 'progress' => $progress]);
        }

        /*
         * Insert in contacts
         */
        $this->jobRepository->setJobStep($jobId, step: 5, payload: ['status' => 'inserting_contacts']);

        $classMetadata = $this->em->getClassMetadata(Contact::class);

        // Static mapping to be used on inserts (and not on conflict updates)
        $insertMapping = [
            'id' => 'nextval(\'community_contacts_id_seq\')',
            'uuid' => 'gen_random_uuid()',
            'organization_id' => $organization->getId(),
            'contact_additional_emails' => $db->quote('[]'),
            'account_confirmed' => 'false',
            'settings_by_project' => $db->quote('[]'),
            'metadata_custom_fields' => $db->quote('[]'),
            'created_at' => 'CURRENT_TIMESTAMP',
            'updated_at' => 'CURRENT_TIMESTAMP',
        ];

        // Values mapping to be used on inserts and conflict updates (including defaults)
        $valuesMapping = [
            'settings_receive_newsletters' => 'true',
            'settings_receive_sms' => 'true',
            'settings_receive_calls' => 'true',
        ];

        foreach (array_keys($columnsMapping) as $column) {
            $valuesMapping = $this->applyInsertMapping($classMetadata, $valuesMapping, $column);
        }

        $insertColumns = array_merge(array_keys($insertMapping), array_keys($valuesMapping));
        $selectColumns = array_merge(array_values($insertMapping), array_values($valuesMapping));

        $selectDistinct = '';
        if (isset($columnsMapping['email'])) {
            $selectDistinct = ' DISTINCT ON (email) ';
        }

        $conflictUpdateSet = ['updated_at = CURRENT_TIMESTAMP'];
        foreach (array_keys($valuesMapping) as $col) {
            $conflictUpdateSet[] = $col.' = EXCLUDED.'.$col;
        }

        $db->executeStatement('
            INSERT INTO community_contacts ('.implode(', ', $insertColumns).')
            SELECT '.$selectDistinct.' '.implode(', ', $selectColumns).' FROM '.$tableName.'
            ON CONFLICT (organization_id, email) DO UPDATE SET '.implode(', ', $conflictUpdateSet)
        );

        /*
         * Insert in tags
         */
        $this->jobRepository->setJobStep($jobId, step: 6, payload: ['status' => 'inserting_tags']);

        $tags = [];

        if (isset($columnsMapping['metadataTag'])) {
            $tags = array_merge(array_column(
                $db->executeQuery('SELECT DISTINCT metadatatag AS tag FROM '.$tableName)
                    ->fetchAllAssociative(),
                'tag',
            ));
        }

        if (isset($columnsMapping['metadataTagsList'])) {
            $tags = array_merge(array_column(
                $db->executeQuery('SELECT DISTINCT regexp_split_to_table(metadatatagslist, \',\') AS tag FROM '.$tableName)
                    ->fetchAllAssociative(),
                'tag',
            ));
        }

        if ($tags) {
            $values = [];

            foreach ($tags as $tag) {
                if (!trim($tag) || !($slug = $this->slugger->slug($tag)->lower()->toString())) {
                    continue;
                }

                $row = [
                    'nextval(\'community_tags_id_seq\')',
                    $organization->getId(),
                    $db->quote($tag),
                    $db->quote($slug),
                    'CURRENT_TIMESTAMP',
                    'CURRENT_TIMESTAMP',
                ];

                $values[] = '('.implode(', ', $row).')';
            }

            foreach (array_chunk($values, 5_000) as $chunk) {
                $db->executeStatement('
                    INSERT INTO community_tags (id, organization_id, name, slug, created_at, updated_at)
                    VALUES '.implode(', ', $chunk).'
                    ON CONFLICT DO NOTHING
                ');
            }
        }

        /*
         * Link tags
         */
        $this->jobRepository->setJobStep($jobId, step: 7, payload: ['status' => 'linking_tags']);

        if (isset($columnsMapping['metadataTag'])) {
            $db->executeStatement('
                INSERT INTO community_contacts_tags (tag_id, contact_id) 
                SELECT DISTINCT t.id AS tag_id, c.id AS contact_id
                FROM (
                    SELECT email, metadatatag AS tag
                    FROM '.$tableName.'
                ) i
                LEFT JOIN community_tags t ON t.organization_id = '.$organization->getId().' AND t.name = i.tag
                LEFT JOIN community_contacts c ON c.organization_id = '.$organization->getId().' AND c.email = i.email
                WHERE t.id IS NOT NULL AND c.id IS NOT NULL
                ON CONFLICT DO NOTHING
            ');
        }

        if (isset($columnsMapping['metadataTagsList'])) {
            $db->executeStatement('
                INSERT INTO community_contacts_tags (tag_id, contact_id) 
                SELECT DISTINCT t.id AS tag_id, c.id AS contact_id
                FROM (
                    SELECT email, regexp_split_to_table(metadatatagslist, \',\') AS tag
                    FROM '.$tableName.'
                ) i
                LEFT JOIN community_tags t ON t.organization_id = '.$organization->getId().' AND t.name = i.tag
                LEFT JOIN community_contacts c ON c.organization_id = '.$organization->getId().' AND c.email = i.email
                WHERE t.id IS NOT NULL AND c.id IS NOT NULL
                ON CONFLICT DO NOTHING
            ');
        }

        /*
         * Resolve areas
         */
        $this->jobRepository->setJobStep($jobId, step: 8, payload: ['status' => 'resolving_areas']);

        // Resolve zip codes, when possible
        $db->executeStatement('
            UPDATE community_contacts c
            SET area_id = (SELECT id FROM areas WHERE tree_root = c.address_country_id AND name = c.address_zip_code)
            WHERE c.organization_id = '.$organization->getId().' AND c.address_country_id IS NOT NULL AND c.address_zip_code IS NOT NULL AND c.email IN (
                SELECT DISTINCT email FROM '.$tableName.'
            )
        ');

        // Use countries otherwise, when possible
        $db->executeStatement('
            UPDATE community_contacts c
            SET area_id = address_country_id
            WHERE c.organization_id = '.$organization->getId().' AND c.area_id IS NULL AND c.address_country_id IS NOT NULL AND c.email IN (
                SELECT DISTINCT email FROM '.$tableName.'
            )
        ');

        // Set default area if configured
        if ($default = $import->getArea()) {
            $db->executeStatement('
                UPDATE community_contacts c
                SET area_id = '.$default->getId().'
                WHERE c.organization_id = '.$organization->getId().' AND c.area_id IS NULL AND c.email IN (
                    SELECT DISTINCT email FROM '.$tableName.'
                )
            ');
        }

        /*
         * Clean (not in try/catch to keep the table in case of failure for debug)
         */
        $this->jobRepository->setJobStep($jobId, step: 9, payload: ['status' => 'cleaning']);
        @unlink($localFile);
        $db->executeStatement('DROP TABLE IF EXISTS '.$tableName);

        /*
         * Stats count
         */
        $this->bus->dispatch(new RefreshContactStatsMessage($organization->getId()));

        /*
         * Indexing
         */
        $orgaUuid = $organization->getUuid()->toRfc4122();

        // Creating ndjson batches
        $this->jobRepository->setJobStep($jobId, step: 9, payload: ['status' => 'indexing_batching']);

        // Create empty batches to ensure the creation of an index of organizations even without contacts
        $batches = $this->crmIndexer->createIndexingBatchesForOrganization($orgaUuid);

        // Create new index version
        $this->jobRepository->setJobStep($jobId, step: 10, payload: ['status' => 'indexing_uploading']);
        $newVersion = $this->crmIndexer->createIndexVersion($orgaUuid);

        // Upload ndjson files
        $tasks = [];
        foreach ($batches[$orgaUuid] ?? [] as $file) {
            $tasks[] = $this->crmIndexer->indexBatch($orgaUuid, $newVersion, $file);
        }

        // Wait for indexing to finish
        $this->jobRepository->setJobStep($jobId, step: 11, payload: ['status' => 'indexing_waiting']);
        if ($tasks) {
            $this->crmIndexer->waitForIndexing(array_filter($tasks));
        }

        // Create organization members search keys and swap live index version
        $this->crmIndexer->bumpIndexVersion($orgaUuid, $newVersion);

        $this->jobRepository->finishJob($jobId);

        return true;
    }

    private function downloadImportFile(Import $import): File
    {
        $tempFile = sys_get_temp_dir().'/citipo-import-'.$import->getId().'.'.$import->getFile()->getExtension();
        file_put_contents($tempFile, $this->cdnStorage->readStream($import->getFile()->getPathname()));

        return new File($tempFile);
    }

    private function normalizeValue(string $type, $value, ?string $country): ?string
    {
        $v = u($value)->trim()->replace('`', '')->replace('×', '')->replace('✂', '');

        switch ($type) {
            case 'ignored':
                return null;

            case 'email':
                return $v->slice(0, 250)->lower()->toString();

            case 'profileFormalTitle':
                return $v->slice(0, 20)->toString();

            case 'addressZipCode':
                return $v->replace(' ', '')->slice(0, 20)->toString();

            case 'profileFirstName':
            case 'profileMiddleName':
            case 'profileLastName':
            case 'profileCompany':
            case 'profileJobTitle':
            case 'socialFacebook':
            case 'socialTwitter':
            case 'socialLinkedIn':
            case 'socialTelegram':
            case 'socialWhatsapp':
            case 'addressStreetLine1':
            case 'addressStreetLine2':
                return $v->slice(0, 150)->toString();

            case 'profileFirstNameSlug':
            case 'profileMiddleNameSlug':
            case 'profileLastNameSlug':
            case 'profileCompanySlug':
            case 'profileJobTitleSlug':
            case 'addressStreetLine1Slug':
            case 'addressStreetLine2Slug':
                return $this->slugger->slug($v->slice(0, 150))->lower()->toString();

            case 'addressCity':
                return Address::formatCityName($v->slice(0, 150)->toString());

            case 'addressCountry':
            case 'contactPhone':
            case 'contactWorkPhone':
                return $v->slice(0, 50)->toString();

            case 'parsedContactPhone':
            case 'parsedContactWorkPhone':
                $parsed = PhoneNumber::parse(
                    $v->replace('.', '')->replace(' ', '')->replace('-', '')->slice(0, 50)->toString(),
                    $country ?: 'FR',
                );

                return $parsed ? PhoneNumber::format($parsed) : null;

            case 'profileBirthdate':
                try {
                    return (new \DateTime($v->toString()))->format('Y-m-d');
                } catch (\Exception) {
                    return null;
                }

            case 'profileGender':
                // Try to guess the gender based on the first letter of the word
                $map = ['h' => 'male', 'm' => 'male', 'f' => 'female'];

                return $map[$v->slice(0, 1)->lower()->toString()] ?? null;

            case 'settingsReceiveNewsletters':
            case 'settingsReceiveSms':
            case 'settingsReceiveCalls':
                $value = $v->lower()->toString();

                if (!$value
                    || '0' === $value
                    || 'false' === $value
                    || 'f' === $value
                    || 'null' === $value
                    || 'n' === $value
                    || 'non' === $value
                    || 'no' === $value) {
                    return '0';
                }

                return '1';

            case 'metadataComment':
            case 'metadataTag':
                return $v->toString();

            case 'metadataTagsList':
                return u(',')->join(array_map(
                    static fn (UnicodeString $s) => $s->trim(),
                    $v->split(','),
                ));
        }

        throw new \InvalidArgumentException('Invalid type '.$type);
    }

    private function applyInsertMapping(ClassMetadata $classMetadata, array $mapping, string $type): array
    {
        switch ($type) {
            case 'settingsReceiveNewsletters':
            case 'settingsReceiveSms':
            case 'settingsReceiveCalls':
                $mapping[$classMetadata->getColumnName($type)] = '(CASE WHEN '.strtolower($type).' = \'1\' THEN true ELSE false END)';
                break;

            case 'profileBirthdate':
                $mapping[$classMetadata->getColumnName($type)] = strtolower($type).'::date';
                break;

            case 'addressCountry':
                $mapping['address_country_id'] = '(SELECT a.id FROM areas a '.
                    'WHERE a.type = \'country\' AND '.
                    '(LOWER(addresscountry) = LOWER(a.code) or LOWER(addresscountry) = LOWER(a.name)) '.
                    'LIMIT 1)';
                break;

                // Use parsed phone and parsed work phone if parsed successfully, original value otherwise
            case 'contactPhone':
                $mapping['contact_phone'] = '(CASE WHEN parsedcontactphone IS NOT NULL THEN parsedcontactphone ELSE contactphone END)';
                break;

            case 'contactWorkPhone':
                $mapping['contact_work_phone'] = '(CASE WHEN parsedcontactworkphone IS NOT NULL THEN parsedcontactworkphone ELSE contactworkphone END)';
                break;

            case 'email':
            case 'profileFormalTitle':
            case 'profileFirstName':
            case 'profileMiddleName':
            case 'profileLastName':
            case 'profileCompany':
            case 'profileJobTitle':
            case 'profileGender':
            case 'parsedContactPhone':
            case 'parsedContactWorkPhone':
            case 'socialFacebook':
            case 'socialTwitter':
            case 'socialLinkedIn':
            case 'socialTelegram':
            case 'socialWhatsapp':
            case 'addressStreetLine1':
            case 'addressStreetLine2':
            case 'addressZipCode':
            case 'addressCity':
            case 'metadataComment':
            case 'profileFirstNameSlug':
            case 'profileMiddleNameSlug':
            case 'profileLastNameSlug':
            case 'profileCompanySlug':
            case 'profileJobTitleSlug':
            case 'addressStreetLine1Slug':
            case 'addressStreetLine2Slug':
                $mapping[$classMetadata->getColumnName($type)] = strtolower($type);
                break;
        }

        return $mapping;
    }
}
