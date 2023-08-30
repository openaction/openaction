<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Bridge\Uploadcare\MockUploadcare;
use App\Community\ImportExport\Consumer\ImportMessage;
use App\Entity\Community\Import;
use App\Repository\Community\ImportRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ImportControllerTest extends WebTestCase
{
    public function testImportProcess()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /*
         * Upload
         */
        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/import');
        $this->assertResponseIsSuccessful();

        // Start upload
        MockUploadcare::setMockFile(__DIR__.'/../../../../Fixtures/import/contacts-map-columns.xlsx');

        $prepareUrl = Json::decode($crawler->filter('[data-react-props-value]')->attr('data-react-props-value'))['prepareUrl'];
        $client->request('POST', $prepareUrl.'&fileUuid=ece8cee0-7bc4-43da-b923-db3f57af9a9b&fileName=contacts-map-columns.xlsx');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = Json::decode($client->getResponse()->getContent());
        $this->assertStringEndsWith('/columns', $data['redirectUrl']);

        /** @var Import $import */
        $import = static::getContainer()->get(ImportRepository::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($import);
        $this->assertSame(
            [
                'ignored', 'email', 'profileFormalTitle', 'profileFirstName', 'profileMiddleName',
                'profileLastName', 'profileBirthdate', 'profileGender', 'profileCompany', 'profileJobTitle',
                'contactPhone', 'contactWorkPhone', 'socialFacebook', 'socialTwitter', 'socialLinkedIn',
                'socialTelegram', 'socialWhatsapp', 'addressStreetLine1', 'addressStreetLine2',
                'addressZipCode', 'addressCity', 'addressCountry', 'settingsReceiveNewsletters',
                'settingsReceiveSms', 'settingsReceiveCalls', 'metadataComment', 'metadataTagsList', 'metadataTag',
            ],
            $import->getHead()->getColumns()
        );

        $this->assertSame(
            [
                'IGNORED', 'FabienneBeaujolie1@jourrapide.com', 'Mme', 'Fabienne1', 'Miron', 'Beaujolie',
                '1961-09-07 00:00:00', 'f', 'Total Yard Maintenance', 'Private detective', '03.15.35.41.79',
                '+33 15 35 41 79', 'https://facebook.com/FabienMiron', 'https://twitter.com/FabienMiron',
                'https://linkedin.com/FabienMiron', 'FabienMiron', '03.15.35.41.79', '5, Rue du Limas',
                '', '21200', 'BEAUNE', 'France', '1', '0', '', 'Custom comment', 'Blue, Red, Black', 'Green',
            ],
            $import->getHead()->getFirstLines()[0]
        );

        $this->assertCount(5, $import->getHead()->getFirstLines());

        /*
         * Columns
         */
        $crawler = $client->request('GET', $data['redirectUrl']);
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Start the import');
        $client->submit($button->form(), [
            'import_metadata[columnsTypes][0]' => 'ignored',
            'import_metadata[columnsTypes][1]' => 'email',
            'import_metadata[columnsTypes][2]' => 'profileFormalTitle',
            'import_metadata[columnsTypes][3]' => 'profileFirstName',
            'import_metadata[columnsTypes][4]' => 'profileMiddleName',
            'import_metadata[columnsTypes][5]' => 'profileLastName',
            'import_metadata[columnsTypes][6]' => 'profileBirthdate',
            'import_metadata[columnsTypes][7]' => 'profileGender',
            'import_metadata[columnsTypes][8]' => 'profileCompany',
            'import_metadata[columnsTypes][9]' => 'profileJobTitle',
            'import_metadata[columnsTypes][10]' => 'contactPhone',
            'import_metadata[columnsTypes][11]' => 'contactWorkPhone',
            'import_metadata[columnsTypes][12]' => 'socialFacebook',
            'import_metadata[columnsTypes][13]' => 'socialTwitter',
            'import_metadata[columnsTypes][14]' => 'socialLinkedIn',
            'import_metadata[columnsTypes][15]' => 'socialTelegram',
            'import_metadata[columnsTypes][16]' => 'socialWhatsapp',
            'import_metadata[columnsTypes][17]' => 'addressStreetLine1',
            'import_metadata[columnsTypes][18]' => 'addressStreetLine2',
            'import_metadata[columnsTypes][19]' => 'addressZipCode',
            'import_metadata[columnsTypes][20]' => 'addressCity',
            'import_metadata[columnsTypes][21]' => 'addressCountry',
            'import_metadata[columnsTypes][22]' => 'settingsReceiveNewsletters',
            'import_metadata[columnsTypes][23]' => 'settingsReceiveSms',
            'import_metadata[columnsTypes][24]' => 'settingsReceiveCalls',
            'import_metadata[columnsTypes][25]' => 'metadataComment',
            'import_metadata[columnsTypes][26]' => 'metadataTagsList',
            'import_metadata[columnsTypes][27]' => 'metadataTag',
            'import_metadata[areaId]' => 36778547219895752, // France
        ]);

        $this->assertResponseRedirects();
        $this->assertStringEndsWith('/progress', $client->getResponse()->headers->get('Location'));

        $import = static::getContainer()->get(ImportRepository::class)->find($import->getId());
        $this->assertSame(
            [
                'ignored', 'email', 'profileFormalTitle', 'profileFirstName', 'profileMiddleName',
                'profileLastName', 'profileBirthdate', 'profileGender', 'profileCompany', 'profileJobTitle',
                'contactPhone', 'contactWorkPhone', 'socialFacebook', 'socialTwitter', 'socialLinkedIn',
                'socialTelegram', 'socialWhatsapp', 'addressStreetLine1', 'addressStreetLine2',
                'addressZipCode', 'addressCity', 'addressCountry', 'settingsReceiveNewsletters',
                'settingsReceiveSms', 'settingsReceiveCalls', 'metadataComment', 'metadataTagsList', 'metadataTag',
            ],
            $import->getHead()->getMatchedColumns(),
        );

        // Check the import message watch dispatched
        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var ImportMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(ImportMessage::class, $message);
        $this->assertSame($import->getId(), $message->getImportId());

        // Check the progress display
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
