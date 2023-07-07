<?php

namespace App\Tests\Community\Printing\Consumer;

use App\Community\Printing\Consumer\ImportPreflightResultHandler;
use App\Community\Printing\Consumer\ImportPreflightResultMessage;
use App\Entity\Community\PrintingCampaign;
use App\Repository\Community\PrintingCampaignRepository;
use App\Tests\KernelTestCase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class ImportPreflightResultHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ImportPreflightResultHandler::class);

        // Shouldn't fail
        $this->assertTrue($handler(new ImportPreflightResultMessage('23aaeb06-c2ed-4608-983d-451baa0636a5')));
    }

    public function testConsumeOk()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        $this->prepareFixtureDirectory(__DIR__.'/../../../Fixtures/printing/preflight_out/ok-pdf');

        $handler = static::getContainer()->get(ImportPreflightResultHandler::class);
        $this->assertTrue($handler(new ImportPreflightResultMessage($campaign->getUuid())));

        $campaign = $this->getCampaign();
        $this->assertNotNull($campaign->getBat());
        $this->assertEmpty($campaign->getBatErrors());
        $this->assertEmpty($campaign->getBatWarnings());

        // Report should have been moved
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Un Bon À Tirer est prêt à être examiné pour la commande n°4730F1DF');
    }

    public function testConsumeWarningPdf()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        $this->prepareFixtureDirectory(__DIR__.'/../../../Fixtures/printing/preflight_out/warning-pdf');

        $handler = static::getContainer()->get(ImportPreflightResultHandler::class);
        $this->assertTrue($handler(new ImportPreflightResultMessage($campaign->getUuid())));

        $campaign = $this->getCampaign();
        $this->assertNotNull($campaign->getBat());
        $this->assertEmpty($campaign->getBatErrors());
        $this->assertSame(
            ['La résolution d\'image est 97 x 97 ppi - image couleur ou en gamme de gris est inférieur(e) à 150 ppi (1x à la page 1)'],
            $campaign->getBatWarnings()
        );

        // Report should have been moved
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Un Bon À Tirer est prêt à être examiné pour la commande n°4730F1DF');
    }

    public function testConsumeWarningsPdf()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        $this->prepareFixtureDirectory(__DIR__.'/../../../Fixtures/printing/preflight_out/warnings-pdf');

        $handler = static::getContainer()->get(ImportPreflightResultHandler::class);
        $this->assertTrue($handler(new ImportPreflightResultMessage($campaign->getUuid())));

        $campaign = $this->getCampaign();
        $this->assertNotNull($campaign->getBat());
        $this->assertEmpty($campaign->getBatErrors());
        $this->assertSame(
            [
                'La résolution d\'image est 97 x 97 ppi - image couleur ou en gamme de gris est inférieur(e) à 150 ppi (1x à la page 1)',
                'La résolution d\'image est 97 x 97 ppi - image couleur ou en gamme de gris est inférieur(e) à 150 ppi (1x à la page 1)',
            ],
            $campaign->getBatWarnings()
        );

        // Report should have been moved
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Un Bon À Tirer est prêt à être examiné pour la commande n°4730F1DF');
    }

    public function testConsumeErrorNoPdf()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        $this->prepareFixtureDirectory(__DIR__.'/../../../Fixtures/printing/preflight_out/error-nopdf');

        $handler = static::getContainer()->get(ImportPreflightResultHandler::class);
        $this->assertTrue($handler(new ImportPreflightResultMessage($campaign->getUuid())));

        $campaign = $this->getCampaign();
        $this->assertNull($campaign->getBat());
        $this->assertSame(
            ['Des objets transparents ont été trouvés (1x à la page 1)'],
            $campaign->getBatErrors()
        );
        $this->assertEmpty($campaign->getBatWarnings());

        // Report should have been moved
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Un Bon À Tirer est prêt à être examiné pour la commande n°4730F1DF');
    }

    public function testConsumeErrorsPdf()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        $this->prepareFixtureDirectory(__DIR__.'/../../../Fixtures/printing/preflight_out/errors-pdf');

        $handler = static::getContainer()->get(ImportPreflightResultHandler::class);
        $this->assertTrue($handler(new ImportPreflightResultMessage($campaign->getUuid())));

        $campaign = $this->getCampaign();
        $this->assertNotNull($campaign->getBat());
        $this->assertSame(
            [
                'Des objets transparents ont été trouvés (1x à la page 1)',
                'Des objets transparents ont été trouvés (1x à la page 1)',
            ],
            $campaign->getBatErrors()
        );
        $this->assertEmpty($campaign->getBatWarnings());

        // Report should have been moved
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertFalse($this->getLgpStorage()->fileExists('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf'));
        $this->assertTrue($this->getLgpStorage()->fileExists('Preflight/OUT/DONE/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml'));

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Un Bon À Tirer est prêt à être examiné pour la commande n°4730F1DF');
    }

    private function getCampaign(): PrintingCampaign
    {
        return static::getContainer()
            ->get(PrintingCampaignRepository::class)
            ->findOneByUuid('1d73e638-650a-4c38-8f01-16e0bb0fb361')
        ;
    }

    private function prepareFixtureDirectory(string $dirname)
    {
        $this->getLgpStorage()->write(
            'Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml',
            file_get_contents($dirname.'/bat.xml')
        );

        if (file_exists($dirname.'/bat.pdf')) {
            $this->getLgpStorage()->write(
                'Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.pdf',
                file_get_contents($dirname.'/bat.pdf')
            );
        }
    }

    private function getLgpStorage(): FilesystemOperator
    {
        return static::getContainer()->get('lgp.storage');
    }
}
