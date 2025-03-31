<?php

namespace App\Search;

use App\Bridge\Meilisearch\Model\Task;
use App\Entity\Area;

class CrmIndexer
{
    public const INDEXING_TABLE = 'indexing_view';

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
        'parsed_contact_phone',
        'contact_work_phone',
        'parsed_contact_work_phone',

        // Profile
        'profile_first_name',
        'profile_first_name_slug',
        'profile_last_name',
        'profile_last_name_slug',
        'profile_company',
        'profile_company_slug',
        'profile_job_title',
        'profile_job_title_slug',
        'profile_birthdate',
        'profile_birthdate_int',
        'profile_age',
        'profile_gender',
        'profile_nationality',

        // Address
        'address_street_line1_slug',
        'address_street_line2_slug',
        'address_zip_code',
        'address_city',
        'address_country',

        // Status
        'status',

        // Metadata
        'settings_receive_newsletters',
        'settings_receive_sms',
        'settings_receive_calls',
        'created_at',
        'created_at_int',

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
        private readonly Crm\CrmNdJsonBatchFactory $ndJsonBatchFactory,
        private readonly Crm\CrmDataIndexer $dataIndexer,
    ) {
    }

    public static function getIndexName(string $organizationUuid, ?string $version): string
    {
        return sprintf(self::INDEX_NAME_FORMAT, $organizationUuid, $version ?: '');
    }

    /**
     * Create indexing batches for all database contacts.
     *
     * @return array<string, string[]> The list of ndjson batches, indexed by organization UUID.
     */
    public function createIndexingBatchesForAllOrganizations(): array
    {
        return $this->ndJsonBatchFactory->createForAllOrganizations();
    }

    /**
     * Create indexing batches for the given organization contacts.
     *
     * @return array<string, string[]> The list of ndjson batches, indexed by organization UUID.
     */
    public function createIndexingBatchesForOrganization(string $organizationUuid): array
    {
        return $this->ndJsonBatchFactory->createForOrganization($organizationUuid);
    }

    /**
     * Create indexing batches for the given contacts.
     *
     * @return array<string, string[]> The list of ndjson batches, indexed by organization UUID.
     */
    public function createIndexingBatchesForContacts(array $contactsUuids): array
    {
        return $this->ndJsonBatchFactory->createForContacts($contactsUuids);
    }

    /**
     * Index a given ndjson batch in the CRM index of a given organization and version.
     *
     * @param string $organizationUuid The index organization UUID.
     * @param string $version          The index version in which to upload the batch.
     * @param string $filename         The pathname of the ndjson batch to upload.
     *
     * @return ?Task Return quickly a task as the indexing is asynchronous.
     */
    public function indexBatch(string $organizationUuid, string $version, string $filename): ?Task
    {
        return $this->dataIndexer->indexFile($organizationUuid, $version, $filename);
    }

    /**
     * Update contacts in the CRM current index for the organization.
     *
     * @return Task[] Return tasks quickly as the indexing is asynchronous.
     */
    public function synchronizeContacts(string $orgaUuid, string $indexVersion, array $contactsUuids): array
    {
        // Extract normalized data for contacts
        $batches = $this->createIndexingBatchesForContacts($contactsUuids);

        $tasks = [];
        foreach ($batches[$orgaUuid] as $file) {
            if (trim(file_get_contents($file))) {
                $tasks[] = $this->dataIndexer->indexFile($orgaUuid, $indexVersion, $file);
            }
        }

        return array_filter($tasks);
    }

    /**
     * Remove contacts in the CRM current index for the organization.
     *
     * @param array $contactsUuids The list of UUID identifiers of contacts to remove.
     *
     * @return ?Task Return the task quickly as the removal is asynchronous.
     */
    public function removeContacts(string $orgaUuid, string $indexVersion, array $contactsUuids): ?Task
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
     * Atomically bump the index used by the Console for the given organization to a new version.
     * This method also renews keys and tokens to be able to use the new index version.
     */
    public function bumpIndexVersion(string $organizationUuid, string $newVersion): void
    {
        $this->dataIndexer->bumpIndexVersion($organizationUuid, $newVersion);
    }
}
