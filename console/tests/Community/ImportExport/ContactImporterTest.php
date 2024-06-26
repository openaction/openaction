<?php

namespace App\Tests\Community\ImportExport;

use App\Community\ImportExport\Consumer\ImportMessage;
use App\Community\ImportExport\ContactImporter;
use App\Entity\Community\Import;
use App\Entity\Organization;
use App\Form\Community\Model\ImportMetadataData;
use App\Repository\Community\ImportRepository;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ContactImporterTest extends KernelTestCase
{
    public function providePrepareImport()
    {
        yield 'xlsx' => [__DIR__.'/../../Fixtures/import/contacts.xlsx'];
    }

    /**
     * @dataProvider providePrepareImport
     */
    public function testPrepareImport(string $pathname)
    {
        self::bootKernel();

        /** @var FilesystemOperator $storage */
        $storage = self::getContainer()->get('cdn.storage');

        /** @var Organization $organization */
        $organization = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['name' => 'Citipo']);

        /** @var ContactImporter $importer */
        $importer = self::getContainer()->get(ContactImporter::class);

        $import = $importer->prepareImport($organization, new File($pathname));

        // Check metadata
        $this->assertSame(
            [0, 'Email', 'First Name', 'Last Name', 'Gender', 'Country', 'Age', 'Date', 'Id'],
            $import->getHead()->getColumns(),
        );

        $this->assertSame(
            [null, 'email', 'profileFirstName', 'profileLastName', 'profileGender', 'addressCountry', null, null, null],
            $import->getHead()->getMatchedColumns(),
        );

        $this->assertSame(
            [
                [1, 'abril.dulce@citipo.com', 'Dulce', 'Abril', 'Female', 'United States', 32, '15/10/2017', 1562],
                [2, 'mara.hashimoto@citipo.com', 'Mara', 'Hashimoto', 'Female', 'Great Britain', 25, '16/08/2016', 1582],
                [3, 'philip.gent@citipo.com', 'Hervé', 'Gent', 'Male', 'France', 36, '21/05/2015', 2587],
            ],
            $import->getHead()->getFirstLines(),
        );

        // Check uploaded file
        $this->assertTrue($storage->fileExists($import->getFile()->getPathname()));
    }

    public function providePerformancePrepareImport()
    {
        yield 'xlsx' => [__DIR__.'/../../Fixtures/import/contacts-big.xlsx'];
    }

    /**
     * @dataProvider providePerformancePrepareImport
     */
    public function testPerformancePrepareImport(string $pathname)
    {
        self::bootKernel();

        /** @var Organization $organization */
        $organization = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['name' => 'Citipo']);

        /** @var ContactImporter $importer */
        $importer = self::getContainer()->get(ContactImporter::class);

        // Ensure reading the spreadsheet head can be done in the UI process (takes < 0.5 sec)
        $startTime = microtime(true);

        $importer->prepareImport($organization, new File($pathname));

        $this->assertLessThan(1, microtime(true) - $startTime);
    }

    public function testStartImport()
    {
        self::bootKernel();

        /** @var Import $import */
        $import = self::getContainer()->get(ImportRepository::class)->findOneBy([
            'uuid' => 'b25ca589-a613-4e62-ac0b-168b9bdf0339',
        ]);

        /** @var ContactImporter $importer */
        $importer = self::getContainer()->get(ContactImporter::class);

        $data = new ImportMetadataData();
        $data->areaId = 36778547219895752; // France
        $data->columnsTypes = [null, 'email', null, 'profileLastName', 'profileGender', 'addressCountry', null, null, null];

        $importer->startImport($import, $data);

        // Check metadata
        $this->assertSame(36778547219895752, $import->getArea()->getId());
        $this->assertSame(
            [null, 'email', null, 'profileLastName', 'profileGender', 'addressCountry', null, null, null],
            $import->getHead()->getMatchedColumns(),
        );

        // Check dispatched message
        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_importing');

        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(ImportMessage::class, $messages[0]->getMessage());
        $this->assertSame($import->getId(), $messages[0]->getMessage()->getImportId());
    }
}
