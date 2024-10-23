<?php

namespace App\Search\Crm;

use App\Entity\Area;
use App\Search\Crm\Model\BatchCursor;
use App\Search\CrmIndexer;
use App\Util\Json;
use App\Util\Uid;
use Doctrine\DBAL\Connection;
use Symfony\Component\Process\Process;
use Symfony\Component\Uid\Uuid;

class CrmDataParser
{
    private const DUMP_HEADER = [
        'organization',
        'uuid',
        'email',
        'contact_additional_emails',
        'contact_phone',
        'parsed_contact_phone',
        'contact_work_phone',
        'parsed_contact_work_phone',
        'profile_formal_title',
        'profile_first_name',
        'profile_first_name_slug',
        'profile_middle_name',
        'profile_middle_name_slug',
        'profile_last_name',
        'profile_last_name_slug',
        'profile_birthdate',
        'profile_birthdate_int',
        'profile_age',
        'profile_gender',
        'profile_nationality',
        'profile_company',
        'profile_company_slug',
        'profile_job_title',
        'profile_job_title_slug',
        'address_street_line1',
        'address_street_line1_slug',
        'address_street_line2',
        'address_street_line2_slug',
        'address_zip_code',
        'address_city',
        'address_country',
        'social_facebook',
        'social_twitter',
        'social_linked_in',
        'social_telegram',
        'social_whatsapp',
        'picture',
        'email_hash',
        'settings_receive_newsletters',
        'settings_receive_sms',
        'settings_receive_calls',
        'created_at',
        'created_at_int',
        'status',
        'area',
        'tags',
        'projects',
        'opened_emails',
        'clicked_emails',
    ];

    public function __construct(private readonly Connection $db, private readonly string $databaseUrl, private readonly string $cacheDir)
    {
    }

    public function dumpIndexingTableToFile(): string
    {
        $details = parse_url($this->databaseUrl);

        if (file_exists($filename = $this->cacheDir.'/indexing_crm.txt')) {
            unlink($filename);
        }

        $process = new Process([
            'psql', '-U', $details['user'], '-h', $details['host'], '-p', $details['port'],
            '-d', ltrim($details['path'], '/'), '--set=sslmode=require', '-c',
            '\copy (SELECT * FROM '.CrmIndexer::INDEXING_TABLE.' ORDER BY organization) to \''.$filename.'\' DELIMITER \'`\';',
        ]);

        $process->setEnv(['PGPASSWORD' => $details['pass']]);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->mustRun();

        return $filename;
    }

    public function createNdJsonBatchesFromFile(string $filename): array
    {
        $cursor = new BatchCursor($this->cacheDir.'/indexing_crm_%s_%s.ndjson', batchSize: 15_000);

        $file = fopen($filename, 'rb');
        while (false !== ($line = fgets($file))) {
            try {
                $row = array_combine(self::DUMP_HEADER, explode('`', $line));
            } catch (\Exception $e) {
                throw new \RuntimeException(message: sprintf('Invalid data format for line "%s": %s (counts: header=%s line=%s)', $line, $e->getMessage(), count(self::DUMP_HEADER), count(explode('`', $line))), previous: $e);
            }

            foreach ($row as $k => $v) {
                if ('\\N' === trim($v)) {
                    $row[$k] = null;
                }
            }

            // Move cursor
            $cursor->move($row['organization']);

            // Write in batch file
            $cursor->write(Json::encode($this->normalizeDumpedIndexedData($row)));
        }

        return $cursor->getAllFiles();
    }

    public function createNdJsonBatchesFromContacts(array $contactsUuids): array
    {
        $cursor = new BatchCursor($this->cacheDir.'/indexing_crm_%s_%s.ndjson', batchSize: 15_000);

        $stmt = $this->db->executeQuery('
            SELECT * FROM '.CrmIndexer::INDEXING_TABLE." 
            WHERE uuid IN ('".implode("', '", $contactsUuids)."')
        ");

        while ($row = $stmt->fetchAssociative()) {
            // Move organization cursor
            $cursor->move($row['organization']);

            // Write normalized data in batch
            $cursor->write(Json::encode($this->normalizeDumpedIndexedData($row)));
        }

        return $cursor->getAllFiles();
    }

    private function normalizeDumpedIndexedData(array $row): array
    {
        // Parse arrays
        foreach (['contact_additional_emails', 'area', 'tags', 'projects', 'opened_emails', 'clicked_emails'] as $key) {
            $row[$key] = $this->parseDumpedArray($row[$key]);
        }

        // Normalize results to ease usage in CRM
        $normalized = [
            'id' => $row['uuid'],
            'encoded_uuid' => Uid::toBase62(Uuid::fromString($row['uuid'])),

            // Organization
            'organization' => $row['organization'],

            // Contact details
            'email' => $row['email'],
            'contact_additional_emails' => $row['contact_additional_emails'],
            'contact_phone' => $row['contact_phone'],
            'parsed_contact_phone' => $row['parsed_contact_phone'],
            'contact_work_phone' => $row['contact_work_phone'],
            'parsed_contact_work_phone' => $row['parsed_contact_work_phone'],

            // Profile
            'profile_formal_title' => $row['profile_formal_title'],
            'profile_first_name' => $row['profile_first_name'],
            'profile_first_name_slug' => $row['profile_first_name_slug'],
            'profile_middle_name' => $row['profile_middle_name'],
            'profile_middle_name_slug' => $row['profile_middle_name_slug'],
            'profile_last_name' => $row['profile_last_name'],
            'profile_last_name_slug' => $row['profile_last_name_slug'],
            'profile_birthdate' => $row['profile_birthdate'],
            'profile_birthdate_int' => ((int) $row['profile_birthdate_int']) ?: null,
            'profile_age' => ((int) $row['profile_age']) ?: null,
            'profile_gender' => $row['profile_gender'],
            'profile_nationality' => $row['profile_nationality'],
            'profile_company' => $row['profile_company'],
            'profile_company_slug' => $row['profile_company_slug'],
            'profile_job_title' => $row['profile_job_title'],
            'profile_job_title_slug' => $row['profile_job_title_slug'],

            // Address
            'address_street_line1' => $row['address_street_line1'],
            'address_street_line1_slug' => $row['address_street_line1_slug'],
            'address_street_line2' => $row['address_street_line2'],
            'address_street_line2_slug' => $row['address_street_line2_slug'],
            'address_zip_code' => $row['address_zip_code'],
            'address_city' => $row['address_city'],
            'address_country' => $row['address_country'],

            // Socials
            'social_facebook' => $row['social_facebook'],
            'social_twitter' => $row['social_twitter'],
            'social_linked_in' => $row['social_linked_in'],
            'social_telegram' => $row['social_telegram'],
            'social_whatsapp' => $row['social_whatsapp'],

            // Picture
            'picture' => $row['picture'],
            'email_hash' => $row['email_hash'],

            // Status
            'status' => $row['status'],

            // Metadata
            'settings_receive_newsletters' => in_array($row['settings_receive_newsletters'], [true, 't'], true),
            'settings_receive_calls' => in_array($row['settings_receive_calls'], [true, 't'], true),
            'settings_receive_sms' => in_array($row['settings_receive_sms'], [true, 't'], true),
            'created_at' => $row['created_at'],
            'created_at_int' => ((int) $row['created_at_int']) ?: null,

            // Area
            'area_'.Area::TYPE_COUNTRY => null,
            'area_'.Area::TYPE_COUNTRY.'_code' => null,
            'area_'.Area::TYPE_PROVINCE => null,
            'area_'.Area::TYPE_PROVINCE.'_name' => null,
            'area_'.Area::TYPE_DISTRICT => null,
            'area_'.Area::TYPE_DISTRICT.'_name' => null,
            'area_'.Area::TYPE_COMMUNITY => null,
            'area_'.Area::TYPE_COMMUNITY.'_name' => null,
            'area_'.Area::TYPE_ZIP_CODE => null,
            'area_'.Area::TYPE_ZIP_CODE.'_name' => null,

            // Tags
            'tags' => array_map('intval', array_column($row['tags'], 0)),
            'tags_names' => array_map('trim', array_column($row['tags'], 1)),

            // Projects
            'projects' => array_column($row['projects'], 0),
            'projects_names' => array_map('trim', array_column($row['projects'], 1)),

            // Emails
            'opened_emails' => $row['opened_emails'],
            'clicked_emails' => $row['clicked_emails'],
        ];

        // Expand area tree
        foreach ($row['area'] ?: [] as [$id, $code, $name, $type]) {
            $normalized['area_'.$type] = (int) $id;

            if (Area::TYPE_COUNTRY === $type) {
                $normalized['area_'.$type.'_code'] = $code;
            } else {
                $normalized['area_'.$type.'_name'] = $name;
            }
        }

        return $normalized;
    }

    private function parseDumpedArray(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $parts = array_map('trim', explode('✂', $value));

        // strpos is much faster than str_contains (big impact on large payloads)
        if (false === strpos($value, '×')) {
            return $parts;
        }

        foreach ($parts as $k => $v) {
            $parts[$k] = array_map('trim', explode('×', $v));
        }

        return $parts;
    }
}
