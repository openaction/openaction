<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Community\Printing\Consumer\DownloadSourceMessage;
use App\Community\Printing\Consumer\RequestOrderProductionMessage;
use App\Entity\Community\PrintingCampaign;
use App\Repository\Community\PrintingCampaignRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class BatControllerTest extends WebTestCase
{
    private const TO_VALIDATE_UUID = 'e28316fb-a32d-4fd5-885f-7d9f925e1c94';

    public function testAcceptTriggersProduction()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::TO_VALIDATE_UUID.'/bat');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form')->form(), []);
        $this->assertResponseRedirects();

        // Should have updated local database
        /** @var PrintingCampaign $campaign */
        $campaign = self::getContainer()->get(PrintingCampaignRepository::class)->findOneBy(['uuid' => self::TO_VALIDATE_UUID]);
        $this->assertNotNull($campaign->getBatValidatedAt());
        $this->assertTrue($campaign->getPrintingOrder()->allBatValidated());

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_printing');
        $this->assertCount(1, $messages = $transport->get());
        /* @var RequestOrderProductionMessage $message */
        $this->assertInstanceOf(RequestOrderProductionMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($campaign->getPrintingOrder()->getId(), $message->getOrderId());

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Votre commande n°8AA012A7 vient d\'être envoyée en production');
    }

    public function testReconfigure()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // Check content
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::TO_VALIDATE_UUID.'/bat/reconfigure');
        $this->assertResponseIsSuccessful();

        // Start upload
        $uploadUrl = Json::decode($crawler->filter('[data-react-props-value]')->attr('data-react-props-value'))['uploadUrl'];
        $client->request('POST', $uploadUrl.'&fileUuid=ece8cee0-7bc4-43da-b923-db3f57af9a9b');

        // Source and preview should have been removed
        $campaign = self::getContainer()->get(PrintingCampaignRepository::class)->findOneBy(['uuid' => self::TO_VALIDATE_UUID]);
        $this->assertNull($campaign->getBat());
        $this->assertNull($campaign->getBatErrors());
        $this->assertNull($campaign->getBatWarnings());
        $this->assertNull($campaign->getSource());
        $this->assertNull($campaign->getSourceError());
        $this->assertNull($campaign->getPreview());

        // Should have dispatched
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_printing')->get());

        /* @var DownloadSourceMessage $message */
        $this->assertInstanceOf(DownloadSourceMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($campaign->getId(), $message->getCampaignId());
        $this->assertSame('ece8cee0-7bc4-43da-b923-db3f57af9a9b', $message->getFileUuid());
        $this->assertTrue($message->isFix());
    }
}
