<?php

namespace App\Tests\Controller\Console\Project\Configuration;

use App\Bridge\Uploadcare\MockUploadcare;
use App\Repository\Community\ContentImportRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Website\ImportExport\Consumer\ContentImportMessage;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ImportContentControllerTest extends WebTestCase
{
    public function testImportProcess(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/content-import/wordpress');
        self::assertResponseIsSuccessful();

        MockUploadcare::setMockFile(__DIR__.'/../../../../Fixtures/import/import-content-wordpress-example-file.xml');

        $prepareUrl = Json::decode($crawler->filter('[data-react-props-value]')->attr('data-react-props-value'))['prepareUrl'];
        $client->request('POST', $prepareUrl.'&fileUuid=ece8cee0-7bc4-43da-b923-db3f57af9a9b&fileName=import-content-wordpress-example-file.xml');

        self::assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = Json::decode($client->getResponse()->getContent());
        $this->assertStringEndsWith('/settings', $data['redirectUrl']);

        $import = static::getContainer()->get(ContentImportRepository::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($import);

        $crawler = $client->request('GET', $data['redirectUrl']);
        self::assertResponseIsSuccessful();

        $button = $crawler->selectButton('Start the import');
        $client->submit($button->form(), [
            'content_import[postSaveStatus]' => 'save_as_original',
            'content_import[keepCategories]' => 'keep_categories_yes',
        ]);

        self::assertResponseRedirects();
        $this->assertStringEndsWith('/progress', $client->getResponse()->headers->get('Location'));

        $import = static::getContainer()->get(ContentImportRepository::class)->find($import->getId());
        $this->assertSame('save_as_original', $import->getSettings()['postSaveStatus']);

        // Check the import message watch dispatched
        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_importing');

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var ContentImportMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(ContentImportMessage::class, $message);
        $this->assertSame($import->getId(), $message->getImportId());

        // Check the progress display
        $client->followRedirect();
        self::assertResponseIsSuccessful();
    }
}
