<?php

namespace App\Tests\Community\Printing\Consumer;

use App\Billing\BillingManager;
use App\Community\Printing\Consumer\RequestOrderProductionHandler;
use App\Community\Printing\Consumer\RequestOrderProductionMessage;
use App\Community\Printing\PrintingPriceCalculator;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Community\PrintingCampaign;
use App\Form\Billing\Model\UpdateBillingDetailsData;
use App\Platform\Companies;
use App\Repository\Community\PrintingCampaignRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class RequestOrderProductionHandlerTest extends KernelTestCase
{
    private const UNADDRESSED_NOENVELOP_NOQR = '4dbb20d4-8c71-4f73-993a-296485abcd5d';
    private const UNADDRESSED_ENVELOPED_UNIQUEQR = 'a826aa93-f863-4060-9adf-5f8c19e6edac';

    private const FIXTURE_FILE = __DIR__.'/../../../Fixtures/printing/campaign_door.pdf';

    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(RequestOrderProductionHandler::class);

        // Shouldn't fail
        $this->assertTrue($handler(new RequestOrderProductionMessage(0)));
    }

    public function testConsumeUnaddressedNoEnvelopNoQr()
    {
        self::bootKernel();

        $campaign = $this->prepareCampaign(self::UNADDRESSED_NOENVELOP_NOQR, UpdateBillingDetailsData::createFromArray([
            'name' => 'Titouan Galopin',
            'email' => 'titouan.galopin@citipo.com',
            'streetLine1' => '49 Rue de Ponthieu',
            'postalCode' => '75008',
            'city' => 'PARIS',
            'country' => 'FR',
        ]));

        // Handle
        $handler = static::getContainer()->get(RequestOrderProductionHandler::class);
        $handler(new RequestOrderProductionMessage($campaign->getPrintingOrder()->getId()));

        // Should have created the request on LGP
        /** @var FilesystemOperator $lgpStorage */
        $lgpStorage = static::getContainer()->get('lgp.storage');
        $this->assertTrue($lgpStorage->fileExists('Production/IN/TODO/'.$campaign->getPrintingOrder()->getUuid().'.zip'));

        $zip = $this->extractZipFile($lgpStorage->read('Production/IN/TODO/'.$campaign->getPrintingOrder()->getUuid().'.zip'));

        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Infos_Commande_MasterPrint PUBLIC "-//W3C//DTD HTML 4.01//EN" "">
<Infos_Commande_MasterPrint>
    <Dossier>
        <COMMANDE_GLOBALE>7e3617e3-b147-4f53-864c-1550d65ddbc4</COMMANDE_GLOBALE>
        <COMMANDE_ARTICLE>4dbb20d4-8c71-4f73-993a-296485abcd5d</COMMANDE_ARTICLE>
        <CODE_TYPE_ARTICLE>AP</CODE_TYPE_ARTICLE>
        <PARTI>LREM</PARTI>
        <NOM_CANDIDAT><![CDATA[Jean Duguet]]></NOM_CANDIDAT>
        <PRENOM_CANDIDAT />
        <DEPARTEMENT_CODE>35</DEPARTEMENT_CODE>
        <DEPARTEMENT_LIB><![CDATA[Ille-et-Vilaine]]></DEPARTEMENT_LIB>
        <CIRCONSCRIPTION>2</CIRCONSCRIPTION>
        <RAISONLIV><![CDATA[Titouan Galopin]]></RAISONLIV>
        <RAISONLIV2 />
        <ADRESSLIV1><![CDATA[49 Rue de Ponthieu]]></ADRESSLIV1>
        <ADRESSLIV2><![CDATA[CPGT SAS]]></ADRESSLIV2>
        <CPLIV><![CDATA[75008]]></CPLIV>
        <VILLELIV><![CDATA[PARIS]]></VILLELIV>
        <COPAYSLIV><![CDATA[FR]]></COPAYSLIV>
        <PAYSLIV><![CDATA[FR]]></PAYSLIV>
        <EMAILLIV><![CDATA[titouan.galopin@citipo.com]]></EMAILLIV>
        <TELLIV><![CDATA[0606060606]]></TELLIV>
        <PORTABLELIV><![CDATA[0606060606]]></PORTABLELIV>
        <REFCOM>LEGISLATIVES 2022</REFCOM>
        <DTCOM>'.$campaign->getCreatedAt()->format('Ymd').'</DTCOM>
        <DATEXP>'.$campaign->getCreatedAt()->modify('+3 days')->format('Ymd').'</DATEXP>
        <QTE_ARTICLE>2</QTE_ARTICLE>
        <DESIG_1><![CDATA[AP Jean Duguet]]></DESIG_1>
        <FICHIERPDF>source.pdf</FICHIERPDF>
        <RectoVerso>RV</RectoVerso>
        <NombreDePages>2</NombreDePages>
        <HauteurFinale>210.0</HauteurFinale>
        <LargeurFinale>60.0</LargeurFinale>
    </Dossier>
</Infos_Commande_MasterPrint>
',
            $zip[$campaign->getUuid().'.xml'],
        );
    }

    public function testConsumeUnaddressedEnvelopedUniqueQr()
    {
        self::bootKernel();

        $campaign = $this->prepareCampaign(self::UNADDRESSED_ENVELOPED_UNIQUEQR, UpdateBillingDetailsData::createFromArray([
            'name' => 'CPGT',
            'email' => 'billing@citipo.com',
            'streetLine1' => '49 Rue de Ponthieu',
            'streetLine2' => 'Etage 1',
            'postalCode' => '75008',
            'city' => 'PARIS',
            'country' => 'FR',
        ]));

        // Handle
        $handler = static::getContainer()->get(RequestOrderProductionHandler::class);
        $handler(new RequestOrderProductionMessage($campaign->getPrintingOrder()->getId()));

        // Should have created the request on LGP
        /** @var FilesystemOperator $lgpStorage */
        $lgpStorage = static::getContainer()->get('lgp.storage');
        $this->assertTrue($lgpStorage->fileExists('Production/IN/TODO/'.$campaign->getPrintingOrder()->getUuid().'.zip'));

        $zip = $this->extractZipFile($lgpStorage->read('Production/IN/TODO/'.$campaign->getPrintingOrder()->getUuid().'.zip'));

        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Infos_Commande_MasterPrint PUBLIC "-//W3C//DTD HTML 4.01//EN" "">
<Infos_Commande_MasterPrint>
    <Dossier>
        <COMMANDE_GLOBALE>9557ff76-88a0-42aa-9c08-c4ce31e5e8a6</COMMANDE_GLOBALE>
        <COMMANDE_ARTICLE>a826aa93-f863-4060-9adf-5f8c19e6edac</COMMANDE_ARTICLE>
        <CODE_TYPE_ARTICLE>AO</CODE_TYPE_ARTICLE>
        <PARTI>LREM</PARTI>
        <NOM_CANDIDAT><![CDATA[Jean Duguet]]></NOM_CANDIDAT>
        <PRENOM_CANDIDAT />
        <DEPARTEMENT_CODE>35</DEPARTEMENT_CODE>
        <DEPARTEMENT_LIB><![CDATA[Ille-et-Vilaine]]></DEPARTEMENT_LIB>
        <CIRCONSCRIPTION>2</CIRCONSCRIPTION>
        <RAISONLIV><![CDATA[Poster Name]]></RAISONLIV>
        <RAISONLIV2 />
        <ADRESSLIV1><![CDATA[Poster 49 Rue de Ponthieu]]></ADRESSLIV1>
        <ADRESSLIV2><![CDATA[Poster CPGT SAS]]></ADRESSLIV2>
        <CPLIV><![CDATA[75010]]></CPLIV>
        <VILLELIV><![CDATA[POSTER-PARIS]]></VILLELIV>
        <COPAYSLIV><![CDATA[FR]]></COPAYSLIV>
        <PAYSLIV><![CDATA[FR]]></PAYSLIV>
        <EMAILLIV><![CDATA[titouan.galopin@citipo.com]]></EMAILLIV>
        <TELLIV><![CDATA[0606060606]]></TELLIV>
        <PORTABLELIV><![CDATA[0606060606]]></PORTABLELIV>
        <REFCOM>LEGISLATIVES 2022</REFCOM>
        <DTCOM>'.$campaign->getCreatedAt()->format('Ymd').'</DTCOM>
        <DATEXP>'.$campaign->getCreatedAt()->modify('+3 days')->format('Ymd').'</DATEXP>
        <QTE_ARTICLE>4</QTE_ARTICLE>
        <DESIG_1><![CDATA[AO Jean Duguet]]></DESIG_1>
        <FICHIERPDF>source.pdf</FICHIERPDF>
        <RectoVerso>R</RectoVerso>
        <NombreDePages>1</NombreDePages>
        <HauteurFinale>841.0</HauteurFinale>
        <LargeurFinale>594.0</LargeurFinale>
    </Dossier>
</Infos_Commande_MasterPrint>
',
            $zip[$campaign->getUuid().'.xml'],
        );
    }

    private function prepareCampaign(string $uuid, UpdateBillingDetailsData $billingDetails): PrintingCampaign
    {
        /** @var PrintingCampaign $campaign */
        $campaign = static::getContainer()->get(PrintingCampaignRepository::class)->findOneByUuid($uuid);
        $this->assertInstanceOf(PrintingCampaign::class, $campaign);

        $order = $campaign->getPrintingOrder();
        $orga = $order->getProject()->getOrganization();

        // Populate uploaded files with real files
        /** @var FilesystemOperator $storage */
        $storage = self::getContainer()->get('cdn.storage');
        $storage->write('print-campaign_door.pdf', file_get_contents(self::FIXTURE_FILE));
        $storage->write('print-official_ballot.pdf', file_get_contents(self::FIXTURE_FILE));
        $storage->write('print-official_poster.pdf', file_get_contents(self::FIXTURE_FILE));

        // Update orga billing details
        $orga->applyBillingDetailsUpdate($billingDetails);
        self::getContainer()->get(EntityManagerInterface::class)->persist($orga);
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        // Populate the billing order
        $order->setOrder(self::getContainer()->get(BillingManager::class)->createMollieOrder(
            Companies::CITIPO,
            $orga,
            new OrderRecipient('Titouan', 'Galopin', 'titouan.galopin@citipo.com', 'fr'),
            OrderAction::print($order->getUuid()->toRfc4122()),
            self::getContainer()->get(PrintingPriceCalculator::class)->createOrderLines($campaign->getPrintingOrder()),
        ));
        self::getContainer()->get(EntityManagerInterface::class)->persist($order);
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        return $campaign;
    }

    private function extractZipFile(string $zipContent): array
    {
        $fs = new Filesystem();
        $fs->dumpFile($file = sys_get_temp_dir().'/tmp.zip', $zipContent);

        $extractedTo = sys_get_temp_dir().'/tmp-extract';
        if ($fs->exists($extractedTo)) {
            $fs->remove($extractedTo);
        }

        $fs->mkdir($extractedTo);

        $archive = new \ZipArchive();
        $archive->open($file);
        $archive->extractTo($extractedTo);

        $files = [];
        foreach (Finder::create()->files()->in($extractedTo)->ignoreDotFiles(true) as $file) {
            $files[$file->getRelativePathname()] = $file->getContents();
        }

        return $files;
    }
}
