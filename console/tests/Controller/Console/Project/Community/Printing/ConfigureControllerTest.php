<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Community\Printing\Consumer\DownloadSourceMessage;
use App\Entity\Community\PrintingCampaign;
use App\Repository\Community\PrintingCampaignRepository;
use App\Tests\WebTestCase;
use App\Util\Json;

class ConfigureControllerTest extends WebTestCase
{
    private const CAMPAIGN_DOOR_UUID = '4dbb20d4-8c71-4f73-993a-296485abcd5d';

    public function testUpload()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // Check content
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::CAMPAIGN_DOOR_UUID.'/configure');
        $this->assertResponseIsSuccessful();

        /** @var PrintingCampaign $campaign */
        $campaign = self::getContainer()->get(PrintingCampaignRepository::class)->findOneBy(['uuid' => self::CAMPAIGN_DOOR_UUID]);
        $this->assertNotEmpty($campaign->getSource());
        $this->assertNotEmpty($campaign->getPreview());

        // Start upload
        $uploadUrl = Json::decode($crawler->filter('[data-react-props-value]')->attr('data-react-props-value'))['uploadUrl'];
        $client->request('POST', $uploadUrl.'&fileUuid=ece8cee0-7bc4-43da-b923-db3f57af9a9b');

        // Source and preview should have been removed
        $campaign = self::getContainer()->get(PrintingCampaignRepository::class)->findOneBy(['uuid' => self::CAMPAIGN_DOOR_UUID]);
        $this->assertNull($campaign->getSource());
        $this->assertNull($campaign->getSourceError());
        $this->assertNull($campaign->getPreview());

        // Should have dispatched
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_printing')->get());

        /* @var DownloadSourceMessage $message */
        $this->assertInstanceOf(DownloadSourceMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($campaign->getId(), $message->getCampaignId());
        $this->assertSame('ece8cee0-7bc4-43da-b923-db3f57af9a9b', $message->getFileUuid());
        $this->assertFalse($message->isFix());
    }
}
