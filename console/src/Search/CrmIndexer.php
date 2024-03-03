<?php

namespace App\Search;

use App\Bridge\Meilisearch\Model\Task;
use App\Entity\Area;

class CrmIndexer
{
    public const INDEXING_TABLE = 'indexing_crm';

    public const INDEX_NAME_FORMAT = 'crm_%s_%s';

    public const SEARCHABLE_ATTRIBUTES = [
        'encoded_uuid',

        // Contact details
        'email',
        'contact_additional_emails',
        'contact_phone',

        // Profile
        'profile_first_name',
        'profile_middle_name',
        'profile_last_name',
        'profile_company',
        'profile_job_title',

        // Address
        'address_street_line1',
        'address_street_line2',
        'address_zip_code',
        'address_city',
        'address_country',

        // Socials
        'social_facebook',
        'social_twitter',
        'social_linked_in',
        'social_telegram',
        'social_whatsapp',
    ];

    public const FILTERABLE_ATTRIBUTES = [
        // Organization
        'organization',

        // Contact details
        'email',
        'contact_additional_emails',
        'contact_phone',

        // Profile
        'profile_first_name',
        'profile_last_name',
        'profile_company',
        'profile_job_title',
        'profile_birthdate',
        'profile_birthdate_int',
        'profile_age',
        'profile_gender',
        'profile_nationality',

        // Address
        'address_street_line1',
        'address_street_line2',
        'address_zip_code',
        'address_city',
        'address_country',

        // Status
        'status',

        // Metadata
        'settings_receive_newsletters',
        'settings_receive_calls',
        'settings_receive_sms',
        'created_at',

        // Area
        'area_'.Area::TYPE_COUNTRY,
        'area_'.Area::TYPE_COUNTRY.'_code',
        'area_'.Area::TYPE_PROVINCE,
        'area_'.Area::TYPE_PROVINCE.'_name',
        'area_'.Area::TYPE_DISTRICT,
        'area_'.Area::TYPE_DISTRICT.'_name',
        'area_'.Area::TYPE_COMMUNITY,
        'area_'.Area::TYPE_COMMUNITY.'_name',
        'area_'.Area::TYPE_ZIP_CODE,
        'area_'.Area::TYPE_ZIP_CODE.'_name',

        // Tags
        'tags',
        'tags_names',

        // Projects
        'projects',
        'projects_names',

        // Opened and clicked emails
        'opened_emails',
        'clicked_emails',
    ];

    public const SORTABLE_ATTRIBUTES = [
        'created_at',
        'profile_first_name',
        'profile_last_name',
        'email',
    ];

    public function __construct(
        private Crm\CrmDataFactory $dataFactory,
        private Crm\CrmDataParser $dataParser,
        private Crm\CrmDataIndexer $dataIndexer,
    ) {
    }

    public static function getIndexName(string $organizationUuid, ?string $version): string
    {
        return sprintf(self::INDEX_NAME_FORMAT, $organizationUuid, $version ?: '');
    }

    /**
     * Create (or recreate) the indexing table used to export CRM data.
     */
    public function resetIndexingTable(): void
    {
        $this->dataFactory->resetIndexingTable();
    }

    /**
     * Populate the indexing table with data from the database for all organization.
     */
    public function populateIndexingTableForAllOrganizations(): void
    {
        $this->dataFactory->populateIndexingTableForAllOrganizations();
    }

    /**
     * Populate the indexing table with data from the database for a given organization.
     *
     * @param int $organizationId Organization ID used to filter the data put in the indexing table.
     */
    public function populateIndexingTableForOrganization(int $organizationId): void
    {
        $this->dataFactory->populateIndexingTableForOrganization($organizationId);
    }

    /**
     * Dump the indexing table content to a local TXT file that can be processed for indexing.
     *
     * @return string The pathname of the dumped file.
     */
    public function dumpIndexingTableToFile(): string
    {
        return $this->dataParser->dumpIndexingTableToFile();
    }

    /**
     * Parse a given dumped local TXT file and create ndjson batch files from it for indexing.
     *
     * @param string $filename The pathname of the dumped file.
     *
     * @return array<string, string[]> The list of ndjson files, indexed by organization UUID.
     */
    public function createNdJsonBatchesFromFile(string $filename): array
    {
        return $this->dataParser->createNdJsonBatchesFromFile($filename);
    }

    /**
     * Create a new version of the CRM index for the given organization.
     *
     * @param string $organizationUuid The organization UUID to create a version for.
     *
     * @return string The new version created.
     */
    public function createIndexVersion(string $organizationUuid): string
    {
        return $this->dataIndexer->createIndexVersion($organizationUuid);
    }

    /**
     * Index a given ndjson file in the CRM index of a given organization and version.
     *
     * @param string $organizationUuid The index organization UUID.
     * @param string $version          The index version in which to upload the file.
     * @param string $filename         The pathname of the ndjson file to upload.
     *
     * @return Task Return quickly a task as the indexing is asynchronous.
     */
    public function indexFile(string $organizationUuid, string $version, string $filename): Task
    {
        return $this->dataIndexer->indexFile($organizationUuid, $version, $filename);
    }

    /**
     * Update contacts in the CRM current index for the organization.
     *
     * @param array $contactsIdentifiers The map of UUID => ID identifiers of contacts to update.
     *
     * @return Task[] Return tasks quickly as the indexing is asynchronous.
     */
    public function updateDocuments(string $orgaUuid, string $indexVersion, array $contactsIdentifiers): array
    {
        $uuids = array_keys($contactsIdentifiers);
        $ids = array_values($contactsIdentifiers);

        // Remove previous indexing table rows
        $this->dataFactory->removeContactsBatchIndexing($uuids);

        // Populate indexing table for contacts
        $this->dataFactory->populateIndexingTableForContactsBatch($ids);

        // Extract normalized data for contacts
        $batches = $this->dataParser->createNdJsonBatchesFromContacts($uuids);

        $tasks = [];
        foreach ($batches[$orgaUuid] as $file) {
            if (trim(file_get_contents($file))) {
                $tasks[] = $this->dataIndexer->indexFile($orgaUuid, $indexVersion, $file);
            }
        }

        return $tasks;
    }

    /**
     * Remove contacts in the CRM current index for the organization.
     *
     * @param array $contactsUuids The list of UUID identifiers of contacts to remove.
     *
     * @return Task] Return the task quickly as the removal is asynchronous.
     */
    public function removeDocuments(string $orgaUuid, string $indexVersion, array $contactsUuids): Task
    {
        return $this->dataIndexer->unindexDocuments($orgaUuid, $indexVersion, $contactsUuids);
    }

    /**
     * Wait for given tasks to finish.
     *
     * @param Task[] $tasks
     */
    public function waitForIndexing(array $tasks)
    {
        $this->dataIndexer->waitForIndexing($tasks);
    }

    /**
     * Atomically bump the index used by the Console for the given organization to a new version.
     * This method also renews keys and tokens to be able to use the new index version.
     */
    public function bumpIndexVersion(string $organizationUuid, string $newVersion): void
    {
        $this->dataIndexer->bumpIndexVersion($organizationUuid, $newVersion);
    }
}
