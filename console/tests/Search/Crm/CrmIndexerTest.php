<?php

namespace App\Tests\Search\Crm;

use App\Entity\Community\Contact;
use App\Repository\Community\ContactRepository;
use App\Search\CrmIndexer;
use App\Tests\KernelTestCase;
use App\Util\Json;

class CrmIndexerTest extends KernelTestCase
{
    public function testCreateIndexingBatchesForAllOrganizations(): void
    {
        self::bootKernel();

        /** @var CrmIndexer $indexer */
        $indexer = self::getContainer()->get(CrmIndexer::class);

        $batches = $indexer->createIndexingBatchesForAllOrganizations();

        $counts = array_map('count', $batches);
        ksort($counts);

        $this->assertSame(
            [
                '219025aa-7fe2-4385-ad8f-31f386720d10' => 1,
                '307c3c05-1873-4e81-ae7d-a1644fa8c5a7' => 1,
                '682746ea-3e2f-4e5b-983b-6548258a2033' => 1,
                'a54ee91a-1c37-48a1-a75d-119ac8ac798e' => 1,
                'cbeb774c-284c-43e3-923a-5a2388340f91' => 1,
                'eafd4a15-7812-4468-aae1-d11217667be0' => 1,
            ],
            $counts,
        );

        foreach ($batches as $files) {
            foreach ($files as $file) {
                $this->assertFileExists($file);
            }
        }

        // Check first line
        $ndjson = file_get_contents($batches['219025aa-7fe2-4385-ad8f-31f386720d10'][0], 'rb');

        $lines = array_filter(explode("\n", trim($ndjson)));
        $this->assertCount(6, $lines);

        $tchalut = [];
        foreach ($lines as $line) {
            $this->assertJson($line);

            if (str_contains($line, 'tchalut@yahoo.fr')) {
                $tchalut = Json::decode($line);
            }
        }

        $this->assertArrayHasKey('projects', $tchalut);
        $this->assertArrayHasKey('projects_names', $tchalut);

        sort($tchalut['projects']);
        sort($tchalut['projects_names']);

        /** @var Contact $contact */
        $contact = self::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'tchalut@yahoo.fr']);

        $this->assertSame(
            [
                'id' => 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2',
                'encoded_uuid' => '75klpBHn7DottVkejf0LDu',
                'organization' => '219025aa-7fe2-4385-ad8f-31f386720d10',
                'email' => 'tchalut@yahoo.fr',
                'contact_additional_emails' => [],
                'contact_phone' => '+33 7 57 59 46 29',
                'parsed_contact_phone' => '+33757594629',
                'contact_work_phone' => null,
                'parsed_contact_work_phone' => null,
                'profile_formal_title' => null,
                'profile_first_name' => 'Théodore',
                'profile_first_name_slug' => 'theodore',
                'profile_middle_name' => null,
                'profile_middle_name_slug' => null,
                'profile_last_name' => 'Chalut',
                'profile_last_name_slug' => 'chalut',
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
                'social_twitter' => '@theodorechalut',
                'social_linked_in' => 'theodore.chalut',
                'social_telegram' => null,
                'social_whatsapp' => '+33600000000',
                'picture' => null,
                'email_hash' => '6a0ee01e6bb5653ed43ad71195571643',
                'status' => 'u',
                'settings_receive_newsletters' => false,
                'settings_receive_sms' => false,
                'settings_receive_calls' => true,
                'created_at' => $contact->getCreatedAt()->format('Y-m-d H:i:s'),
                'created_at_int' => (int) $contact->getCreatedAt()->format('Ymd'),
                'area_country' => 36778547219895752,
                'area_country_code' => 'fr',
                'area_province' => 64795327863947811,
                'area_province_name' => 'Île-de-France',
                'area_district' => 65636974309722332,
                'area_district_name' => 'Hauts-de-Seine',
                'area_community' => 3066627596119454,
                'area_community_name' => 'Arrondissement de Nanterre',
                'area_zip_code' => 39389989938296926,
                'area_zip_code_name' => '92110',
                'tags' => $contact->getMetadataTagsIds(),
                'tags_names' => ['ContainsTagInside'],
                'projects' => [
                    '151f1340-9ad6-47c7-a8a5-838ff955eae7',
                    '62241741-7504-4cb9-9d56-a417a3d07bb3',
                    'e816bcc6-0568-46d1-b0c5-917ce4810a87',
                ],
                'projects_names' => [
                    'Citipo',
                    'Trial',
                    'Île-de-France',
                ],
                'opened_emails' => ['e0340fcd-f0ec-4ee8-b0f9-7545f3a53cc5'],
                'clicked_emails' => [],
            ],
            $tchalut
        );
    }

    public function testCreateIndexingBatchesForOrganizations(): void
    {
        self::bootKernel();

        /** @var CrmIndexer $indexer */
        $indexer = self::getContainer()->get(CrmIndexer::class);

        $batches = $indexer->createIndexingBatchesForOrganizations([
            '219025aa-7fe2-4385-ad8f-31f386720d10',
            'cbeb774c-284c-43e3-923a-5a2388340f91',
        ]);

        $counts = array_map('count', $batches);
        ksort($counts);

        $this->assertSame(
            [
                '219025aa-7fe2-4385-ad8f-31f386720d10' => 1,
                'cbeb774c-284c-43e3-923a-5a2388340f91' => 1,
            ],
            $counts,
        );

        foreach ($batches as $files) {
            foreach ($files as $file) {
                $this->assertFileExists($file);
            }
        }
    }

    public function testCreateIndexingBatchesForOneOrganization(): void
    {
        self::bootKernel();

        /** @var CrmIndexer $indexer */
        $indexer = self::getContainer()->get(CrmIndexer::class);

        $batches = $indexer->createIndexingBatchesForOrganization('219025aa-7fe2-4385-ad8f-31f386720d10');

        $counts = array_map('count', $batches);
        ksort($counts);

        $this->assertSame(
            ['219025aa-7fe2-4385-ad8f-31f386720d10' => 1],
            $counts,
        );

        foreach ($batches as $files) {
            foreach ($files as $file) {
                $this->assertFileExists($file);
            }
        }

        // Check first line
        $ndjson = file_get_contents($batches['219025aa-7fe2-4385-ad8f-31f386720d10'][0], 'rb');

        $lines = array_filter(explode("\n", trim($ndjson)));
        $this->assertCount(6, $lines);

        $tchalut = [];
        foreach ($lines as $line) {
            $this->assertJson($line);

            if (str_contains($line, 'tchalut@yahoo.fr')) {
                $tchalut = Json::decode($line);
            }
        }

        $this->assertArrayHasKey('projects', $tchalut);
        $this->assertArrayHasKey('projects_names', $tchalut);

        sort($tchalut['projects']);
        sort($tchalut['projects_names']);

        /** @var Contact $contact */
        $contact = self::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'tchalut@yahoo.fr']);

        $this->assertSame(
            [
                'id' => 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2',
                'encoded_uuid' => '75klpBHn7DottVkejf0LDu',
                'organization' => '219025aa-7fe2-4385-ad8f-31f386720d10',
                'email' => 'tchalut@yahoo.fr',
                'contact_additional_emails' => [],
                'contact_phone' => '+33 7 57 59 46 29',
                'parsed_contact_phone' => '+33757594629',
                'contact_work_phone' => null,
                'parsed_contact_work_phone' => null,
                'profile_formal_title' => null,
                'profile_first_name' => 'Théodore',
                'profile_first_name_slug' => 'theodore',
                'profile_middle_name' => null,
                'profile_middle_name_slug' => null,
                'profile_last_name' => 'Chalut',
                'profile_last_name_slug' => 'chalut',
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
                'social_twitter' => '@theodorechalut',
                'social_linked_in' => 'theodore.chalut',
                'social_telegram' => null,
                'social_whatsapp' => '+33600000000',
                'picture' => null,
                'email_hash' => '6a0ee01e6bb5653ed43ad71195571643',
                'status' => 'u',
                'settings_receive_newsletters' => false,
                'settings_receive_sms' => false,
                'settings_receive_calls' => true,
                'created_at' => $contact->getCreatedAt()->format('Y-m-d H:i:s'),
                'created_at_int' => (int) $contact->getCreatedAt()->format('Ymd'),
                'area_country' => 36778547219895752,
                'area_country_code' => 'fr',
                'area_province' => 64795327863947811,
                'area_province_name' => 'Île-de-France',
                'area_district' => 65636974309722332,
                'area_district_name' => 'Hauts-de-Seine',
                'area_community' => 3066627596119454,
                'area_community_name' => 'Arrondissement de Nanterre',
                'area_zip_code' => 39389989938296926,
                'area_zip_code_name' => '92110',
                'tags' => $contact->getMetadataTagsIds(),
                'tags_names' => ['ContainsTagInside'],
                'projects' => [
                    '151f1340-9ad6-47c7-a8a5-838ff955eae7',
                    '62241741-7504-4cb9-9d56-a417a3d07bb3',
                    'e816bcc6-0568-46d1-b0c5-917ce4810a87',
                ],
                'projects_names' => [
                    'Citipo',
                    'Trial',
                    'Île-de-France',
                ],
                'opened_emails' => ['e0340fcd-f0ec-4ee8-b0f9-7545f3a53cc5'],
                'clicked_emails' => [],
            ],
            $tchalut
        );
    }

    public function testCreateIndexingBatchesForContacts(): void
    {
        self::bootKernel();

        /** @var CrmIndexer $indexer */
        $indexer = self::getContainer()->get(CrmIndexer::class);

        $batches = $indexer->createIndexingBatchesForContacts(['e90c2a1c-9504-497d-8354-c9dabc1ff7a2']);

        $counts = array_map('count', $batches);
        ksort($counts);

        $this->assertSame(
            ['219025aa-7fe2-4385-ad8f-31f386720d10' => 1],
            $counts,
        );

        foreach ($batches as $files) {
            foreach ($files as $file) {
                $this->assertFileExists($file);
            }
        }

        // Check first line
        $ndjson = file_get_contents($batches['219025aa-7fe2-4385-ad8f-31f386720d10'][0], 'rb');

        $lines = array_filter(explode("\n", trim($ndjson)));
        $this->assertCount(1, $lines);

        $line = $lines[0];
        $this->assertJson($line);
        $tchalut = Json::decode($line);

        $this->assertArrayHasKey('projects', $tchalut);
        $this->assertArrayHasKey('projects_names', $tchalut);

        sort($tchalut['projects']);
        sort($tchalut['projects_names']);

        /** @var Contact $contact */
        $contact = self::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'tchalut@yahoo.fr']);

        $this->assertSame(
            [
                'id' => 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2',
                'encoded_uuid' => '75klpBHn7DottVkejf0LDu',
                'organization' => '219025aa-7fe2-4385-ad8f-31f386720d10',
                'email' => 'tchalut@yahoo.fr',
                'contact_additional_emails' => [],
                'contact_phone' => '+33 7 57 59 46 29',
                'parsed_contact_phone' => '+33757594629',
                'contact_work_phone' => null,
                'parsed_contact_work_phone' => null,
                'profile_formal_title' => null,
                'profile_first_name' => 'Théodore',
                'profile_first_name_slug' => 'theodore',
                'profile_middle_name' => null,
                'profile_middle_name_slug' => null,
                'profile_last_name' => 'Chalut',
                'profile_last_name_slug' => 'chalut',
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
                'social_twitter' => '@theodorechalut',
                'social_linked_in' => 'theodore.chalut',
                'social_telegram' => null,
                'social_whatsapp' => '+33600000000',
                'picture' => null,
                'email_hash' => '6a0ee01e6bb5653ed43ad71195571643',
                'status' => 'u',
                'settings_receive_newsletters' => false,
                'settings_receive_sms' => false,
                'settings_receive_calls' => true,
                'created_at' => $contact->getCreatedAt()->format('Y-m-d H:i:s'),
                'created_at_int' => (int) $contact->getCreatedAt()->format('Ymd'),
                'area_country' => 36778547219895752,
                'area_country_code' => 'fr',
                'area_province' => 64795327863947811,
                'area_province_name' => 'Île-de-France',
                'area_district' => 65636974309722332,
                'area_district_name' => 'Hauts-de-Seine',
                'area_community' => 3066627596119454,
                'area_community_name' => 'Arrondissement de Nanterre',
                'area_zip_code' => 39389989938296926,
                'area_zip_code_name' => '92110',
                'tags' => $contact->getMetadataTagsIds(),
                'tags_names' => ['ContainsTagInside'],
                'projects' => [
                    '151f1340-9ad6-47c7-a8a5-838ff955eae7',
                    '62241741-7504-4cb9-9d56-a417a3d07bb3',
                    'e816bcc6-0568-46d1-b0c5-917ce4810a87',
                ],
                'projects_names' => [
                    'Citipo',
                    'Trial',
                    'Île-de-France',
                ],
                'opened_emails' => ['e0340fcd-f0ec-4ee8-b0f9-7545f3a53cc5'],
                'clicked_emails' => [],
            ],
            $tchalut
        );
    }

    public function testCreateIndexingBatchesForOrganizationWithCustomBatchSize(): void
    {
        self::bootKernel();

        /** @var CrmIndexer $indexer */
        $indexer = self::getContainer()->get(CrmIndexer::class);

        $batches = $indexer->createIndexingBatchesForOrganization('219025aa-7fe2-4385-ad8f-31f386720d10', 2);

        $this->assertArrayHasKey('219025aa-7fe2-4385-ad8f-31f386720d10', $batches);
        $this->assertCount(4, $batches['219025aa-7fe2-4385-ad8f-31f386720d10']);

        $linesCount = 0;
        foreach ($batches['219025aa-7fe2-4385-ad8f-31f386720d10'] as $file) {
            $this->assertFileExists($file);
            $linesCount += count(array_filter(explode("\n", trim(file_get_contents($file, 'rb')))));
        }

        $this->assertSame(6, $linesCount);
    }
}
