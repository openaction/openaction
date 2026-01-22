<?php

namespace App\Tests\Controller\Console\Project\Community\Texting;

use App\Bridge\Twilio\MockTwilio;
use App\Bridge\Twilio\TwilioInterface;
use App\Community\Consumer\SendTextingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\TagRepository;
use App\Repository\Community\TextingCampaignRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use App\Util\Json;

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
            'contacts' => ['+33757592064'],
            'expectedCount' => 1,
        ];

        yield 'multiple_contacts' => [
            'member' => 0,
            'areas' => [],
            'tags' => [],
            'contacts' => ['+33757592064', '+33757592579', '+33600000000'],
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
            $tagsIds[] = self::getContainer()->get(TagRepository::class)->findOneBy(['slug' => $tag])->getId();
        }

        $url = '/console/project/'.self::PROJECT_IDF_UUID.'/community/texting/c4d39567-f3ef-4f46-ac2f-d7573a5456d9';
        $url .= '/filter-preview?member='.$member.'&areas='.implode(' ', $areas).'&tags='.implode(' ', $tagsIds).'&contacts='.urlencode(implode(' ', $contacts));

        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = Json::decode($client->getResponse()->getContent());
        $this->assertArrayHasKey('count', $data);
        $this->assertSame($expectedCount, $data['count']);
    }

    public function testSendTest()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(10, $orga->getTextsCreditsBalance());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/texting/c4d39567-f3ef-4f46-ac2f-d7573a5456d9/send-test');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the preview');
        $client->submit($button->form(), ['form[phone]' => '07 57 59 46 25']);
        $this->assertResponseRedirects('/console/project/'.self::PROJECT_IDF_UUID.'/community/texting');

        // Credits should have been used
        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);
        $this->assertSame(9, $orga->getTextsCreditsBalance());

        /** @var MockTwilio $twilio */
        $twilio = self::getContainer()->get(TwilioInterface::class);
        $this->assertCount(1, $twilio->messages);

        $message = $twilio->messages[0];
        $this->assertSame('Go vote for Auralp on 20th and 27th of June!', $message['body']);
        $this->assertSame('+33 7 57 59 46 25', $message['to']);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSendTestNotEnoughCredits()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/texting/197efcd3-00ea-470e-8b47-99f84ff7c128/send-test');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the preview');
        $client->submit($button->form(), ['form[phone]' => '+33757594625']);
        $this->assertResponseRedirects('/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/texting');

        /** @var MockTwilio $twilio */
        $twilio = self::getContainer()->get(TwilioInterface::class);
        $this->assertCount(0, $twilio->messages);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-bg-error:contains("Your organization does not have enough credits to execute this action")');
    }

    public function testSendAll()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91']);
        $this->assertSame(10, $orga->getTextsCreditsBalance());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting/e4a3799d-b217-4389-b89e-beef08bdbbd3/send-all');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the campaign');
        $client->submit($button->form());
        $this->assertResponseRedirects('/console/project/'.self::PROJECT_ACME_UUID.'/community/texting');

        // Credits should have been used
        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91']);
        $this->assertSame(7, $orga->getTextsCreditsBalance());

        // Test the dispatching of the message
        $transport = self::getContainer()->get('messenger.transport.async_texting');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(SendTextingCampaignMessage::class, $messages[0]->getMessage());

        /** @var EmailingCampaign $campaign */
        $campaign = self::getContainer()->get(TextingCampaignRepository::class)->findOneBy(['uuid' => 'e4a3799d-b217-4389-b89e-beef08bdbbd3']);
        $this->assertSame($campaign->getId(), $messages[0]->getMessage()->getCampaignId());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSendAllNotEnoughCredits()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/texting/197efcd3-00ea-470e-8b47-99f84ff7c128/send-all');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Send the campaign');
        $client->submit($button->form());
        $this->assertResponseRedirects('/console/project/643e47ea-fd9d-4963-958f-05970de2f88b/community/texting');

        $transport = self::getContainer()->get('messenger.transport.async_texting');
        $this->assertCount(0, $transport->get());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-bg-error:contains("Your organization does not have enough credits to execute this action")');
    }
}
