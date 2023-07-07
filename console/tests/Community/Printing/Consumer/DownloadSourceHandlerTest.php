<?php

namespace App\Tests\Community\Printing\Consumer;

use App\Bridge\Uploadcare\MockUploadcare;
use App\Community\Printing\Consumer\DownloadSourceHandler;
use App\Community\Printing\Consumer\DownloadSourceMessage;
use App\Entity\Community\PrintingCampaign;
use App\Repository\Community\PrintingCampaignRepository;
use App\Tests\KernelTestCase;

class DownloadSourceHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(DownloadSourceHandler::class);

        // Shouldn't fail
        $this->assertTrue($handler(new DownloadSourceMessage(0, '23aaeb06-c2ed-4608-983d-451baa0636a5')));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        MockUploadcare::setMockFile(__DIR__.'/../../../Fixtures/printing/official_poster_valid.pdf');

        $handler = static::getContainer()->get(DownloadSourceHandler::class);
        $this->assertTrue($handler(new DownloadSourceMessage($campaign->getId(), '23aaeb06-c2ed-4608-983d-451baa0636a5')));

        $campaign = $this->getCampaign();
        $this->assertNotNull($campaign->getSource());
        $this->assertNotNull($campaign->getPreview());
        $this->assertNull($campaign->getSourceError());
    }

    public function testConsumeInvalidPages()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        MockUploadcare::setMockFile(__DIR__.'/../../../Fixtures/printing/official_poster_invalid_pages.pdf');

        $handler = static::getContainer()->get(DownloadSourceHandler::class);
        $this->assertTrue($handler(new DownloadSourceMessage($campaign->getId(), '23aaeb06-c2ed-4608-983d-451baa0636a5')));

        $campaign = $this->getCampaign();
        $this->assertNull($campaign->getSource());
        $this->assertNull($campaign->getPreview());
        $this->assertSame(
            ['Ce fichier PDF ne contient pas le nombre de pages attendu pour ce produit.'],
            $campaign->getSourceError()->getMessages()
        );
    }

    public function testConsumeTooSmall()
    {
        self::bootKernel();

        $campaign = $this->getCampaign();
        MockUploadcare::setMockFile(__DIR__.'/../../../Fixtures/printing/official_poster_too_small.pdf');

        $handler = static::getContainer()->get(DownloadSourceHandler::class);
        $this->assertTrue($handler(new DownloadSourceMessage($campaign->getId(), '23aaeb06-c2ed-4608-983d-451baa0636a5')));

        $campaign = $this->getCampaign();
        $this->assertNull($campaign->getSource());
        $this->assertNull($campaign->getPreview());
        $this->assertSame(
            ['Les dimensions des pages de ce PDF sont trop petites pour réaliser une impression haute définition de celui-ci. Veillez à bien utiliser une définition de 300dpi et les dimensions adaptées au produit commandé.'],
            $campaign->getSourceError()->getMessages()
        );
    }

    private function getCampaign(): PrintingCampaign
    {
        return static::getContainer()
            ->get(PrintingCampaignRepository::class)
            ->findOneByUuid('1d73e638-650a-4c38-8f01-16e0bb0fb361')
        ;
    }
}
