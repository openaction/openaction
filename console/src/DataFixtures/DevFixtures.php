<?php

namespace App\DataFixtures;

use App\Entity\Area;
use App\Entity\Domain;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Theme\WebsiteTheme;
use App\Util\Uid;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Load core test fixtures and add additional ones for development.
 */
class DevFixtures extends AbstractFixtures
{
    protected static int $order = 2;
    protected static array $groups = ['dev'];

    public function doLoad()
    {
        $this->loadAdditionalProjects();
        $this->loadAdditionalContacts();
    }

    private function loadAdditionalProjects()
    {
        $usStates = [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District Of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        ];

        // Example Co
        $orga = $this->findOneBy(Organization::class, ['uuid' => '682746ea-3e2f-4e5b-983b-6548258a2033']);
        $domain = $this->findOneBy(Domain::class, ['name' => 'exampleco.com']);
        $area = $this->findOneBy(Area::class, ['id' => 64795327863947811]);

        $slugger = new AsciiSlugger();
        foreach ($usStates as $code => $name) {
            $this->em->persist(Project::createFixture([
                'uuid' => Uid::fixed($code),
                'theme' => $this->em->getRepository(WebsiteTheme::class)->findOneBy(['uuid' => 'd325bbff-70bf-40a5-ac25-c0259c0aa126']),
                'orga' => $orga,
                'name' => $name,
                'domain' => $domain,
                'subdomain' => $slugger->slug($name)->lower(),
                'area' => $area,
            ]));
        }

        $this->em->flush();
    }

    private function loadAdditionalContacts()
    {
        $faker = Factory::create('fr_FR');

        // Citipo
        $orga = $this->findOneBy(Organization::class, ['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $project = $this->findOneBy(Project::class, ['uuid' => 'e816bcc6-0568-46d1-b0c5-917ce4810a87']);
        $areas = $this->findBy(Area::class, []);
        $country = $this->findOneBy(Area::class, ['name' => 'France']);

        // Use raw SQL for performance and memory usage
        $query = $this->em->getConnection()->prepare('
            INSERT INTO community_contacts (
                uuid, organization_id, area_id, email, profile_first_name, profile_middle_name, profile_last_name,
                account_confirmed, account_password, address_street_line1, address_street_line2, address_zip_code, address_city,
                address_country_id, settings_receive_newsletters, settings_receive_sms, settings_receive_calls,
                metadata_custom_fields, metadata_comment, created_at, updated_at, id, contact_additional_emails,
                settings_by_project
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                nextval(\'community_contacts_id_seq\'), \'[]\', \'{}\'
            )
        ');

        // Creation stat query
        $statQuery = $this->em->getConnection()->prepare('
            INSERT INTO analytics_community_contact_creations (contact_id, organization_id, project_id, is_member, has_phone, receives_newsletter, receives_sms, date, id)
            VALUES (?, ?, ?, ?, false, ?, ?, ?, nextval(\'analytics_community_contact_creations_id_seq\'))
        ');

        for ($i = 0; $i < 300000; ++$i) {
            $hasAccount = $faker->boolean(15);
            $receivesNewsletter = $faker->boolean(85);
            $receivesSms = $faker->boolean(85);
            $date = $faker->dateTimeBetween('-1 year');

            $query->execute([
                Uid::random()->toRfc4122(),
                $orga->getId(),
                $faker->randomElement($areas)->getId(),
                $faker->email(),
                $faker->firstName(),
                $faker->firstName(),
                $faker->lastName(),
                $hasAccount ? ($faker->boolean(80) ? 'true' : 'false') : 'false',
                $hasAccount ? '$argon2id$v=19$m=65536,t=4,p=1$l5SXk8IgaxdsqWw5DEvt7g$UuGNv5dx5zLi886b58Xxit9VzZM7ouJXlQkZVcvAK6w' : null,
                $faker->streetAddress(),
                $faker->secondaryAddress(),
                $faker->postcode(),
                $faker->city(),
                $country->getId(),
                $receivesNewsletter ? 'true' : 'false',
                $receivesSms ? 'true' : 'false',
                $faker->boolean(85) ? 'true' : 'false',
                '[]',
                $faker->boolean(15) ? $faker->sentence() : null,
                $date->format('Y-m-d H:i:s'),
                $date->format('Y-m-d H:i:s'),
            ]);

            $statQuery->execute([
                $this->em->getConnection()->lastInsertId(),
                $orga->getId(),
                $project->getId(),
                $hasAccount ? 'true' : 'false',
                $receivesNewsletter ? 'true' : 'false',
                $receivesSms ? 'true' : 'false',
                $date->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
