<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Entity\Community\EmailAutomation;
use App\Repository\Community\EmailAutomationRepository;
use App\Repository\Community\TagRepository;
use App\Repository\Website\FormRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class EmailAutomationControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('.world-list-row:contains("Admin alert automation")'));
        $this->assertCount(1, $crawler->filter('.world-list-row:contains("Member welcome message")'));
        $this->assertCount(0, $crawler->filter('.world-list-row:contains("Disabled automation")'));
    }

    public function testDisabled()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/disabled');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('.world-list-row:contains("Disabled automation")'));
        $this->assertCount(0, $crawler->filter('.world-list-row:contains("Admin alert automation")'));
        $this->assertCount(0, $crawler->filter('.world-list-row:contains("Contact welcome message")'));
    }

    public function testPreview()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/preview');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('p:contains("Contact [fullName]")');
    }

    public function testCreateFromTemplate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/create-from-template');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Choose this template")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/organization/[0-9a-zA-Z\-]+/community/automations/[0-9a-zA-Z\-]+/content~', $client->getResponse()->headers->get('Location'));
    }

    public function testCreateFromTemplateInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/create-from-template');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Choose this template")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->attr('href').'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testEditMetadataAllContacts()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save details');
        $client->submit($button->form(), [
            'email_automation_meta_data[name]' => 'Renamed',
            'email_automation_meta_data[subject]' => 'Subject',
            'email_automation_meta_data[preview]' => 'Preview',
            'email_automation_meta_data[fromEmail]' => 'edited@gmail.com',
            'email_automation_meta_data[fromName]' => 'Edited',
            'email_automation_meta_data[toEmailType]' => 'everyone',
            'email_automation_meta_data[trigger]' => 'new_contact',
            'email_automation_meta_data[typeFilter]' => 'contact',
        ]);

        $this->assertResponseRedirects('/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata');

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '828e0d22-0fab-4a59-a9d6-9b5dc575680f']);
        $this->assertSame('Renamed', $automation->getName());
        $this->assertSame('Subject', $automation->getSubject());
        $this->assertSame('Preview', $automation->getPreview());
        $this->assertSame('edited@gmail.com', $automation->getFromEmail());
        $this->assertSame('Edited', $automation->getFromName());
        $this->assertNull($automation->getToEmail());
        $this->assertSame(EmailAutomation::TYPE_CONTACT, $automation->getTypeFilter());
        $this->assertNull($automation->getFormFilter());
        $this->assertNull($automation->getTagFilter());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testEditMetadataFormFilter()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata');
        $this->assertResponseIsSuccessful();

        $form = static::getContainer()->get(FormRepository::class)->findOneBy(['uuid' => '60df6024-b46a-4b1b-877b-6d34092da9da']);

        $button = $crawler->selectButton('Save details');
        $client->submit($button->form(), [
            'email_automation_meta_data[name]' => 'Renamed',
            'email_automation_meta_data[subject]' => 'Subject',
            'email_automation_meta_data[preview]' => 'Preview',
            'email_automation_meta_data[fromEmail]' => 'edited@gmail.com',
            'email_automation_meta_data[fromName]' => 'Edited',
            'email_automation_meta_data[toEmailType]' => 'everyone',
            'email_automation_meta_data[typeFilter]' => 'contact',
            'email_automation_meta_data[trigger]' => 'new_form_answer',
            'email_automation_meta_data[formFilter]' => $form->getId(),
        ]);

        $this->assertResponseRedirects('/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata');

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '828e0d22-0fab-4a59-a9d6-9b5dc575680f']);
        $this->assertSame('Renamed', $automation->getName());
        $this->assertSame('Subject', $automation->getSubject());
        $this->assertSame('Preview', $automation->getPreview());
        $this->assertSame('edited@gmail.com', $automation->getFromEmail());
        $this->assertSame('Edited', $automation->getFromName());
        $this->assertNull($automation->getToEmail());
        $this->assertSame(EmailAutomation::TYPE_CONTACT, $automation->getTypeFilter());
        $this->assertSame($form->getId(), $automation->getFormFilter()?->getId());
        $this->assertNull($automation->getTagFilter());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testEditMetadataTagFilter()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata');
        $this->assertResponseIsSuccessful();

        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);

        $button = $crawler->selectButton('Save details');
        $client->submit($button->form(), [
            'email_automation_meta_data[name]' => 'Renamed',
            'email_automation_meta_data[subject]' => 'Subject',
            'email_automation_meta_data[preview]' => 'Preview',
            'email_automation_meta_data[fromEmail]' => 'edited@gmail.com',
            'email_automation_meta_data[fromName]' => 'Edited',
            'email_automation_meta_data[toEmailType]' => 'everyone',
            'email_automation_meta_data[typeFilter]' => 'contact',
            'email_automation_meta_data[trigger]' => 'contact_tagged',
            'email_automation_meta_data[tagFilter]' => $tag->getId(),
        ]);

        $this->assertResponseRedirects('/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata');

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '828e0d22-0fab-4a59-a9d6-9b5dc575680f']);
        $this->assertSame('Renamed', $automation->getName());
        $this->assertSame('Subject', $automation->getSubject());
        $this->assertSame('Preview', $automation->getPreview());
        $this->assertSame('edited@gmail.com', $automation->getFromEmail());
        $this->assertSame('Edited', $automation->getFromName());
        $this->assertNull($automation->getToEmail());
        $this->assertSame(EmailAutomation::TYPE_CONTACT, $automation->getTypeFilter());
        $this->assertSame($tag->getId(), $automation->getTagFilter()?->getId());
        $this->assertNull($automation->getFormFilter());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testEditContent()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#automation-editor');
    }

    public function testUpdateContent()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content/update',
            ['content' => 'my html', 'design' => Json::encode(['body' => []])],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '828e0d22-0fab-4a59-a9d6-9b5dc575680f']);
        $this->assertEquals('my html', $automation->getContent());
        $this->assertEquals(['body' => []], $automation->getUnlayerDesign());
    }

    public function testUpdateContentInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('POST', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content/update');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideUploadImage(): iterable
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
     * @dataProvider provideUploadImage
     */
    public function testUploadImage(int $count, string $filename, int $expectedStatus, bool $expectedAdded)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content/upload', [], [
            'file' => new UploadedFile(__DIR__.'/../../../../Fixtures/upload/'.$filename, $filename),
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);

        if ($expectedAdded) {
            $this->assertJson($json = $client->getResponse()->getContent());
            $this->assertNotEmpty(Json::decode($json)['url']);
        }

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertSame($expectedAdded, count($storage->listContents('.')->toArray()) > 0);
    }

    public function testDisable()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '828e0d22-0fab-4a59-a9d6-9b5dc575680f']);
        $this->assertTrue($automation->isEnabled());

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $row = $crawler->filter('.world-list-row:contains("Admin alert automation")'));
        $this->assertCount(1, $link = $row->selectLink('Disable'));

        $client->click($link->link());
        $this->assertResponseRedirects();

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '828e0d22-0fab-4a59-a9d6-9b5dc575680f']);
        $this->assertFalse($automation->isEnabled());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testEnable()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '5c232818-ebb3-4a07-bb3b-2732082fb26c']);
        $this->assertFalse($automation->isEnabled());

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/disabled');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $row = $crawler->filter('.world-list-row:contains("Disabled automation")'));
        $this->assertCount(1, $link = $row->selectLink('Enable back'));

        $client->click($link->link());
        $this->assertResponseRedirects();

        /** @var EmailAutomation $automation */
        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => '5c232818-ebb3-4a07-bb3b-2732082fb26c']);
        $this->assertTrue($automation->isEnabled());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
