<?php

namespace App\Tests\Search\Consumer;

use App\Entity\Organization;
use App\Entity\Platform\Job;
use App\Repository\OrganizationRepository;
use App\Repository\Platform\JobRepository;
use App\Search\Consumer\ExportCrmBatchHandler;
use App\Search\Consumer\ExportCrmBatchMessage;
use App\Tests\WebTestCase;
use App\Util\Spreadsheet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class ExportCrmBatchHandlerTest extends WebTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ExportCrmBatchHandler::class);
        $this->assertTrue($handler(new ExportCrmBatchMessage(0, 0, [])));
    }

    public function testConsumeValid()
    {
        $client = self::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');

        /** @var Job $job */
        $job = static::getContainer()->get(JobRepository::class)->startJob('export_batch', 0, 1);
        $this->assertSame('export_batch', $job->getType());
        $this->assertSame(0, $job->getStep());
        $this->assertSame(1, $job->getTotal());
        $this->assertSame(0.0, $job->getProgress());
        $this->assertFalse($job->isFinished());
        $this->assertSame([], $job->getPayload());

        $handler = static::getContainer()->get(ExportCrmBatchHandler::class);
        $this->assertTrue($handler(new ExportCrmBatchMessage(
            $job->getId(),
            $orga->getId(),
            [
                'queryInput' => 'bru',
                'queryFilter' => ['status = \'m\''],
                'querySort' => ['profile_first_name:desc'],
                'params' => [],
            ]
        )));

        // Check job status
        static::getContainer()->get(EntityManagerInterface::class)->refresh($job);
        $this->assertSame('export_batch', $job->getType());
        $this->assertSame(1, $job->getStep());
        $this->assertSame(1, $job->getTotal());
        $this->assertSame(1.0, $job->getProgress());
        $this->assertTrue($job->isFinished());
        $this->assertArrayHasKey('fileUrl', $job->getPayload());

        // Check exported file
        $client->request('GET', $job->getPayload()['fileUrl']);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $client->getInternalResponse()->getHeader('Content-Type'));

        $tempFile = sys_get_temp_dir().'/citipo-tests-export-contacts.xlsx';

        try {
            file_put_contents($tempFile, $client->getInternalResponse()->getContent());
            $this->assertCount(2, $sheet = Spreadsheet::open(new File($tempFile)));
            $this->assertCount(27, $sheet->getFirstLines(2)[1]);
        } finally {
            unlink($tempFile);
        }
    }
}
