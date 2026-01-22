<?php

namespace App\Tests\Controller\Console\Project\Community\Emailing;

use App\Bridge\Sendgrid\MockSendgrid;
use App\Bridge\Sendgrid\SendgridInterface;
use App\Community\Consumer\SendBrevoEmailingCampaignMessage;
use App\Community\Consumer\SendEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use SendGrid\Mail\To;

class SendControllerTest extends WebTestCase
{
    public function provideFilterPreviewCount()
    {
        yield 'all' => [
            'member' => 0,
            'areas' => [],
            'tags' => [],
            'contacts' => [],
            'expectedCount' => 3,
        ];

        yield 'member' => [
            'member' => 1,
            'areas' => [],
            'tags' => [],
            'contacts' => [],
            'expectedCount' => 3,
        ];

        yield 'single_area' => [
            'member' => 0,
            'areas' => ['39389989938296926'], // 92110
            'tags' => [],
            'contacts' => [],
            'expectedCount' => 2,
        ];

        yield 'multiple_areas' => [
            'member' => 0,
            'areas' => ['39389989938296926', '64795327863947811'], // 92110, Ile de France
            'tags' => [],
            'contacts' => [],
            'expectedCount' => 3,
        ];

        yield 'tags' => [
            'member' => 0,
            'areas' => [],
            'tags' => ['exampletag'],
            'contacts' => [],
            'expectedCount' => 1,
        ];

        yield 'contacts' => [
            'member' => 0,
            'areas' => [],
            'tags' => [],
            'contacts' => ['apolline.mousseau@rpr.fr'],
            'expectedCount' => 1,
        ];

        yield 'multiple_contacts' => [
            'member' => 0,
            'areas' => [],
            'tags' => [],
            'contacts' => ['apolline.mousseau@rpr.fr', 'a.compagnon@protonmail.com', 'nonexistent@mail.com'],
            'expectedCount' => 2,
        ];
    }

    /**
     * @dataProvider provideFilterPreviewCount
     */
    public function testFilterPreviewCount(int $member, array $areas, array $tags, array $contacts, int $expectedCount)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $tagsIds = [];
        foreach ($tags as $tag) {
            $tagsIds[] = static::getContainer()->get(TagRepository::class)->findOneBy(['slug' => $tag])->getId();
        }

        $url = '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/2ed86068-e3bc-4db3-9e68-0bff1fd04fb9';
        $url .= '/filter-preview?member='.$member.'&areas='.implode(' ', $areas).'&tags='.implode(' ', $tagsIds).'&contacts='.implode(' ', $contacts);

        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = Json::decode($client->getResponse()->getContent());
        $this->assertArrayHasKey('count', $data);
        $this->assertSame($expectedCount, $data['count']);
    }

    public function testPreview()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/10808026-bbae-4db5-a8ab-8abecb50102c/preview');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('p:contains("Hello world")');
    }

    public function testSendTest()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(1000000, $orga->getCreditsBalance());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/10808026-bbae-4db5-a8ab-8abecb50102c/send-test');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the preview');
        $client->submit($button->form(), ['form[emails]' => 'titouan.galopin@citipo.com, adrien.duguet@citipo.com']);
        $this->assertResponseRedirects('/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');

        // Credits should have been used
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(999998, $orga->getCreditsBalance());

        /** @var MockSendgrid $sendgrid */
        $sendgrid = static::getContainer()->get(SendgridInterface::class);
        $this->assertCount(1, $sendgrid->mails);

        $mail = $sendgrid->mails[0];
        $this->assertSame('Preview - [URGENT] Submit your applications before due date!', $mail->getGlobalSubject()->getSubject());
        $this->assertSame('Jacques BAUER', $mail->getFrom()->getName());
        $this->assertSame('jbauer@citipo.com', $mail->getFrom()->getEmail());
        $this->assertSame('Reply Name', $mail->getReplyTo()->getName());
        $this->assertSame('reply@email.com', $mail->getReplyTo()->getEmail());
        $this->assertCount(1, $contents = $mail->getContents());
        $this->assertStringContainsString('Hello world', $contents[0]->getValue());

        $to = [];
        foreach ($mail->getPersonalizations() as $personalization) {
            $to[] = array_map(fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['adrien.duguet@citipo.com', 'titouan.galopin@citipo.com'], $to);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSendTestNotEnoughCredits()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/emailing/ffb28a07-db46-4c56-aff5-7b7bb3dbfd48/send-test');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the preview');
        $client->submit($button->form(), ['form[emails]' => 'titouan.galopin@citipo.com']);
        $this->assertResponseRedirects('/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/emailing');

        /** @var MockSendgrid $sendgrid */
        $sendgrid = static::getContainer()->get(SendgridInterface::class);
        $this->assertCount(0, $sendgrid->mails);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-bg-error:contains("Your organization does not have enough credits to execute this action")');
    }

    public function testSendAll()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(1000000, $orga->getCreditsBalance());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/10808026-bbae-4db5-a8ab-8abecb50102c/send-all');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the campaign');
        $client->submit($button->form());
        $this->assertResponseRedirects('/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');

        // Credits should have been used
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(999997, $orga->getCreditsBalance());

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(SendEmailingCampaignMessage::class, $messages[0]->getMessage());

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneBy(['uuid' => '10808026-bbae-4db5-a8ab-8abecb50102c']);
        $this->assertSame($campaign->getId(), $messages[0]->getMessage()->getCampaignId());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSendAllNotEnoughCredits()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/emailing/ffb28a07-db46-4c56-aff5-7b7bb3dbfd48/send-all');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the campaign');
        $client->submit($button->form());
        $this->assertResponseRedirects('/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/emailing');

        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $this->assertCount(0, $transport->get());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-bg-error:contains("Your organization does not have enough credits to execute this action")');
    }

    public function testSendMembers()
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91']);
        $this->assertSame(5000, $orga->getCreditsBalance());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/emailing/45b9ea9c-4e62-4d7d-acf1-d7da009f657a/send-all');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the campaign');
        $client->submit($button->form());
        $this->assertResponseRedirects('/console/project/'.self::PROJECT_ACME_UUID.'/community/emailing');

        // Credits should have been used
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91']);
        $this->assertSame(4997, $orga->getCreditsBalance());

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(SendEmailingCampaignMessage::class, $messages[0]->getMessage());

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneBy(['uuid' => '45b9ea9c-4e62-4d7d-acf1-d7da009f657a']);
        $this->assertSame($campaign->getId(), $messages[0]->getMessage()->getCampaignId());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSendAllBrevo()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $orga->setEmailProvider('brevo');
        $orga->setBrevoApiKey('brevo_api_key');
        $orga->setBrevoListId(4242);
        $orga->setBrevoSenderEmail('brevo@citipo.com');
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/10808026-bbae-4db5-a8ab-8abecb50102c/send-all');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the campaign');
        $client->submit($button->form());
        $this->assertResponseRedirects('/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(999997, $orga->getCreditsBalance());

        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(SendBrevoEmailingCampaignMessage::class, $messages[0]->getMessage());

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneBy(['uuid' => '10808026-bbae-4db5-a8ab-8abecb50102c']);
        $this->assertSame($campaign->getId(), $messages[0]->getMessage()->getCampaignId());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
