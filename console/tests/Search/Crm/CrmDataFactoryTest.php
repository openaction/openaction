<?php

namespace App\Tests\Search\Crm;

use App\Search\Crm\CrmDataFactory;
use App\Tests\KernelTestCase;
use Doctrine\DBAL\Connection;

class CrmDataFactoryTest extends KernelTestCase
{
    public function testResetPopulateIndexingTableForAllOrganizations()
    {
        self::bootKernel();

        /** @var CrmDataFactory $dataFactory */
        $dataFactory = self::getContainer()->get(CrmDataFactory::class);

        // Reset
        $dataFactory->resetIndexingTable();
        $this->assertSame(1, $this->countQuery('SELECT COUNT(*) FROM pg_catalog.pg_tables WHERE tablename = \'indexing_crm\''));
        $this->assertSame(0, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));

        // Populate
        $dataFactory->populateIndexingTableForAllOrganizations();
        $this->assertSame(21, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));

        // Check first document
        /** @var Connection $db */
        $db = self::getContainer()->get(Connection::class);
        $document = $db->executeQuery('SELECT * FROM indexing_crm ORDER BY email LIMIT 1')->fetchAssociative();

        $this->assertArrayHasKey('created_at', $document);
        $this->assertArrayHasKey('created_at_int', $document);
        $this->assertArrayHasKey('area', $document);
        $this->assertArrayHasKey('tags', $document);
        $this->assertArrayHasKey('projects', $document);
        unset($document['created_at'], $document['created_at_int'], $document['area'], $document['tags'], $document['projects']);

        $this->assertSame(
            [
                'organization' => '219025aa-7fe2-4385-ad8f-31f386720d10',
                'uuid' => '38dd80c0-b53e-4c29-806f-d2aeca8edb80',
                'email' => 'a.compagnon@protonmail.com',
                'contact_additional_emails' => null,
                'contact_phone' => '+33 7 57 59 25 79',
                'parsed_contact_phone' => '+33757592579',
                'contact_work_phone' => null,
                'parsed_contact_work_phone' => null,
                'profile_formal_title' => null,
                'profile_first_name' => 'André',
                'profile_first_name_slug' => 'andre',
                'profile_middle_name' => null,
                'profile_middle_name_slug' => null,
                'profile_last_name' => 'Compagnon',
                'profile_last_name_slug' => 'compagnon',
                'profile_birthdate' => null,
                'profile_birthdate_int' => null,
                'profile_age' => null,
                'profile_gender' => null,
                'profile_nationality' => null,
                'profile_company' => null,
                'profile_company_slug' => null,
                'profile_job_title' => null,
                'profile_job_title_slug' => null,
                'address_street_line1' => null,
                'address_street_line1_slug' => null,
                'address_street_line2' => null,
                'address_street_line2_slug' => null,
                'address_zip_code' => null,
                'address_city' => null,
                'address_country' => null,
                'social_facebook' => null,
                'social_twitter' => null,
                'social_linked_in' => 'andrecompagnon',
                'social_telegram' => 'someid',
                'social_whatsapp' => null,
                'picture' => null,
                'email_hash' => '00cb95a7877d22c4243587618de318eb',
                'settings_receive_newsletters' => true,
                'settings_receive_sms' => true,
                'settings_receive_calls' => true,
                'status' => 'm',
                'opened_emails' => '95b3f576-c643-45ba-9d5e-c9c44f65fab8',
                'clicked_emails' => '95b3f576-c643-45ba-9d5e-c9c44f65fab8',
            ],
            $document
        );
    }

    public function testResetPopulateIndexingTableForOrganization()
    {
        self::bootKernel();

        /** @var CrmDataFactory $dataFactory */
        $dataFactory = self::getContainer()->get(CrmDataFactory::class);

        /** @var Connection $db */
        $db = self::getContainer()->get(Connection::class);
        $orga = $db->executeQuery('SELECT * FROM organizations WHERE name = \'Citipo\'')->fetchAssociative();

        // Reset
        $dataFactory->resetIndexingTable();
        $this->assertSame(1, $this->countQuery('SELECT COUNT(*) FROM pg_catalog.pg_tables WHERE tablename = \'indexing_crm\''));
        $this->assertSame(0, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));

        // Populate
        $dataFactory->populateIndexingTableForOrganization($orga['id']);
        $this->assertSame(6, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));
    }

    public function testResetPopulateIndexingTableForContactsBatchFullUpdate()
    {
        self::bootKernel();

        /** @var CrmDataFactory $dataFactory */
        $dataFactory = self::getContainer()->get(CrmDataFactory::class);

        /** @var Connection $db */
        $db = self::getContainer()->get(Connection::class);
        $contact = $db->executeQuery('SELECT * FROM community_contacts WHERE email = \'olivie.gregoire@gmail.com\'')->fetchAssociative();

        // Reset
        $dataFactory->resetIndexingTable();
        $this->assertSame(1, $this->countQuery('SELECT COUNT(*) FROM pg_catalog.pg_tables WHERE tablename = \'indexing_crm\''));
        $this->assertSame(0, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));

        // Populate
        $dataFactory->populateIndexingTableForContactsBatch([$contact['id']]);
        $this->assertSame(1, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));
    }

    public function testResetPopulateIndexingTableForContactsBatchTagUpdate()
    {
        self::bootKernel();

        /** @var CrmDataFactory $dataFactory */
        $dataFactory = self::getContainer()->get(CrmDataFactory::class);

        /** @var Connection $db */
        $db = self::getContainer()->get(Connection::class);
        $contact = $db->executeQuery('SELECT * FROM community_contacts WHERE email = \'olivie.gregoire@gmail.com\'')->fetchAssociative();

        // Reset
        $dataFactory->resetIndexingTable();
        $this->assertSame(1, $this->countQuery('SELECT COUNT(*) FROM pg_catalog.pg_tables WHERE tablename = \'indexing_crm\''));
        $this->assertSame(0, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));

        // Populate
        $dataFactory->populateIndexingTableForContactsBatch([$contact['id']], true);
        $this->assertSame(1, $this->countQuery('SELECT COUNT(*) FROM indexing_crm'));
    }

    private function countQuery(string $query): int
    {
        /** @var Connection $db */
        $db = self::getContainer()->get(Connection::class);

        return $db->executeQuery($query)->fetchNumeric()[0];
    }
}
