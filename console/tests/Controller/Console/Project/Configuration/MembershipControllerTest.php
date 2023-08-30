<?php

namespace App\Tests\Controller\Console\Project\Configuration;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\Response;

class MembershipControllerTest extends WebTestCase
{
    public function provideForm(): iterable
    {
        yield 'shuffle' => [[
            'uuid' => 'e816bcc6-0568-46d1-b0c5-917ce4810a87',
            'introduction' => 'Roses are red, Violets are blue, Sugar is sweet, And so are you.',
            'email' => 'membership.form.rule.update',
            'password' => 'membership.form.rule.ignore',
            'profileFirstName' => 'membership.form.rule.optional',
            'profileLastName' => 'membership.form.rule.update',
            'profileFormalTitle' => 'membership.form.rule.ignore',
            'profileMiddleName' => 'membership.form.rule.optional',
            'profileBirthdate' => 'membership.form.rule.update',
            'profileGender' => 'membership.form.rule.ignore',
            'profileNationality' => 'membership.form.rule.optional',
            'profileCompany' => 'membership.form.rule.update',
            'profileJobTitle' => 'membership.form.rule.ignore',
            'contactPhone' => 'membership.form.rule.optional',
            'contactWorkPhone' => 'membership.form.rule.update',
            'socialFacebook' => 'membership.form.rule.ignore',
            'socialTwitter' => 'membership.form.rule.optional',
            'socialLinkedIn' => 'membership.form.rule.update',
            'socialTelegram' => 'membership.form.rule.ignore',
            'socialWhatsapp' => 'membership.form.rule.optional',
            'addressStreetLine1' => 'membership.form.rule.update',
            'addressStreetLine2' => 'membership.form.rule.ignore',
            'addressZipCode' => 'membership.form.rule.optional',
            'addressCity' => 'membership.form.rule.update',
            'addressCountry' => 'membership.form.rule.ignore',
            'settingsReceiveNewsletters' => 'membership.form.rule.optional',
            'settingsReceiveSms' => 'membership.form.rule.update',
            'settingsReceiveCalls' => 'membership.form.rule.ignore',
        ]];
    }

    /**
     * @dataProvider provideForm
     */
    public function testForm($data)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.$data['uuid'].'/configuration/settings/membership/form');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'update_membership_settings[introduction]' => $data['introduction'],
            'update_membership_settings[email]' => $data['email'],
            'update_membership_settings[password]' => $data['password'],
            'update_membership_settings[profileFirstName]' => $data['profileFirstName'],
            'update_membership_settings[profileLastName]' => $data['profileLastName'],
            'update_membership_settings[profileFormalTitle]' => $data['profileFormalTitle'],
            'update_membership_settings[profileMiddleName]' => $data['profileMiddleName'],
            'update_membership_settings[profileBirthdate]' => $data['profileBirthdate'],
            'update_membership_settings[profileGender]' => $data['profileGender'],
            'update_membership_settings[profileNationality]' => $data['profileNationality'],
            'update_membership_settings[profileCompany]' => $data['profileCompany'],
            'update_membership_settings[profileJobTitle]' => $data['profileJobTitle'],
            'update_membership_settings[contactPhone]' => $data['contactPhone'],
            'update_membership_settings[contactWorkPhone]' => $data['contactWorkPhone'],
            'update_membership_settings[socialFacebook]' => $data['socialFacebook'],
            'update_membership_settings[socialTwitter]' => $data['socialTwitter'],
            'update_membership_settings[socialLinkedIn]' => $data['socialLinkedIn'],
            'update_membership_settings[socialTelegram]' => $data['socialTelegram'],
            'update_membership_settings[socialWhatsapp]' => $data['socialWhatsapp'],
            'update_membership_settings[addressStreetLine1]' => $data['addressStreetLine1'],
            'update_membership_settings[addressStreetLine2]' => $data['addressStreetLine2'],
            'update_membership_settings[addressZipCode]' => $data['addressZipCode'],
            'update_membership_settings[addressCity]' => $data['addressCity'],
            'update_membership_settings[addressCountry]' => $data['addressCountry'],
            'update_membership_settings[settingsReceiveNewsletters]' => $data['settingsReceiveNewsletters'],
            'update_membership_settings[settingsReceiveSms]' => $data['settingsReceiveSms'],
            'update_membership_settings[settingsReceiveCalls]' => $data['settingsReceiveCalls'],
        ]);

        // load from data base

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => $data['uuid']]);
        $settings = $project->getMembershipFormSettings();

        $this->assertSame($settings->introduction, $data['introduction']);
        $this->assertSame($settings->profileFormalTitle, $data['profileFormalTitle']);
        $this->assertSame($settings->profileMiddleName, $data['profileMiddleName']);
        $this->assertSame($settings->profileBirthdate, $data['profileBirthdate']);
        $this->assertSame($settings->profileGender, $data['profileGender']);
        $this->assertSame($settings->profileNationality, $data['profileNationality']);
        $this->assertSame($settings->profileCompany, $data['profileCompany']);
        $this->assertSame($settings->profileJobTitle, $data['profileJobTitle']);
        $this->assertSame($settings->contactPhone, $data['contactPhone']);
        $this->assertSame($settings->contactWorkPhone, $data['contactWorkPhone']);
        $this->assertSame($settings->socialFacebook, $data['socialFacebook']);
        $this->assertSame($settings->socialTwitter, $data['socialTwitter']);
        $this->assertSame($settings->socialLinkedIn, $data['socialLinkedIn']);
        $this->assertSame($settings->socialTelegram, $data['socialTelegram']);
        $this->assertSame($settings->socialWhatsapp, $data['socialWhatsapp']);
        $this->assertSame($settings->addressStreetLine1, $data['addressStreetLine1']);
        $this->assertSame($settings->addressStreetLine2, $data['addressStreetLine2']);
        $this->assertSame($settings->addressZipCode, $data['addressZipCode']);
        $this->assertSame($settings->addressCity, $data['addressCity']);
        $this->assertSame($settings->addressCountry, $data['addressCountry']);
        $this->assertSame($settings->settingsReceiveNewsletters, $data['settingsReceiveNewsletters']);
        $this->assertSame($settings->settingsReceiveSms, $data['settingsReceiveSms']);
        $this->assertSame($settings->settingsReceiveCalls, $data['settingsReceiveCalls']);
    }

    public function testHomepage()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership/homepage');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#page-editor');
    }

    public function testHomepageUpdate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership/homepage');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership/homepage/update',
            ['update_membership_main_page' => ['content' => 'hello world']],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => self::PROJECT_CITIPO_UUID]);
        $this->assertEquals('hello world', $project->getMembershipMainPage());
    }

    public function testHomepageUpdateInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership/homepage/update');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideHomepageUploadImage(): iterable
    {
        yield 'pdf' => [
            'count' => 1,
            'filename' => 'document.pdf',
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedAdded' => false,
        ];

        yield 'png' => [
            'count' => 2,
            'filename' => 'mario.png',
            'expectedStatus' => Response::HTTP_OK,
            'expectedAdded' => true,
        ];

        yield 'jpg' => [
            'count' => 3,
            'filename' => 'french.jpg',
            'expectedStatus' => Response::HTTP_OK,
            'expectedAdded' => true,
        ];
    }

    /**
     * @dataProvider provideHomepageUploadImage
     */
    public function testHomepageUploadImage(int $count, string $filename, int $expectedStatus, bool $expectedAdded)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership/homepage/upload?count='.$count, [
            'hidimg-'.$count => base64_encode(file_get_contents(__DIR__.'/../../../../Fixtures/upload/'.$filename)),
            'hidname-'.$count => 'file',
            'hidtype-'.$count => 'png',
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertSame($expectedAdded, count($storage->listContents('.')->toArray()) > 0);
    }
}
