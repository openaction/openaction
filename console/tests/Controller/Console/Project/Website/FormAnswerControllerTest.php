<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Repository\Website\FormAnswerRepository;
use App\Repository\Website\FormRepository;
use App\Tests\WebTestCase;
use App\Util\Spreadsheet;
use Symfony\Component\HttpFoundation\File\File;

class FormAnswerControllerTest extends WebTestCase
{
    private const FORM_SUSTAINABLE_EU_UUID = 'a2b2dbd9-f0b8-435c-ae65-00bc93ad3356';
    private const ANSWER_JEAN_MARTIN_UUID = 'a736779d-43b3-44e2-b2b0-5831ccb5580f';

    public function testResults()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/results/'.self::FORM_SUSTAINABLE_EU_UUID);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));
    }

    public function testExport()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/results/'.self::FORM_SUSTAINABLE_EU_UUID.'/export');

        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $client->getInternalResponse()->getHeader('Content-Type')
        );

        $this->assertEquals(
            'attachment; filename='.date('Y-m-d').'-our-sustainable-europe-answers.xlsx',
            $client->getInternalResponse()->getHeader('Content-Disposition')
        );

        $tempFile = sys_get_temp_dir().'/citipo-tests-export-form-answers.xlsx';

        try {
            file_put_contents($tempFile, $client->getInternalResponse()->getContent());
            $this->assertCount(2, Spreadsheet::open(new File($tempFile)));
        } finally {
            unlink($tempFile);
        }
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/results/'.self::ANSWER_JEAN_MARTIN_UUID.'/view');
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/results/'.self::FORM_SUSTAINABLE_EU_UUID.'');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));

        $form = static::getContainer()->get(FormRepository::class)->findOneBy(['uuid' => self::FORM_SUSTAINABLE_EU_UUID]);
        $this->assertSame(1, static::getContainer()->get(FormAnswerRepository::class)->count(['form' => $form]));

        $client->clickLink('Delete');
        $crawler = $client->followRedirect();
        $this->assertCount(0, $crawler->filter('tbody tr'));
        $this->assertSame(0, static::getContainer()->get(FormAnswerRepository::class)->count(['form' => $form]));
    }
}
