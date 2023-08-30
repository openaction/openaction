<?php

namespace App\Tests\Search\Consumer;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Entity\Organization;
use App\Entity\Platform\Job;
use App\Repository\Community\ContactRepository;
use App\Repository\OrganizationRepository;
use App\Repository\Platform\JobRepository;
use App\Search\Consumer\RemoveCrmBatchHandler;
use App\Search\Consumer\RemoveCrmBatchMessage;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RemoveCrmBatchHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(RemoveCrmBatchHandler::class);
        $this->assertTrue($handler(new RemoveCrmBatchMessage(0, 0, [])));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');

        /** @var Job $job */
        $job = static::getContainer()->get(JobRepository::class)->startJob('remove_batch', 0, 1);
        $this->assertSame('remove_batch', $job->getType());
        $this->assertSame(0, $job->getStep());
        $this->assertSame(1, $job->getTotal());
        $this->assertSame(0.0, $job->getProgress());
        $this->assertFalse($job->isFinished());
        $this->assertSame([], $job->getPayload());

        $handler = static::getContainer()->get(RemoveCrmBatchHandler::class);
        $this->assertTrue($handler(new RemoveCrmBatchMessage(
            $job->getId(),
            $orga->getId(),
            [
                'queryInput' => 'olivie.gregoire@gmail.com',
                'queryFilter' => [],
                'querySort' => ['profile_first_name:desc'],
                'params' => [],
            ],
        )));

        // Clear Doctrine memory
        static::getContainer()->get(EntityManagerInterface::class)->clear();

        // Check database is updated
        $this->assertNull(
            static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'olivie.gregoire@gmail.com'])
        );

        // Check Meilisearch is updated
        $documents = static::getContainer()->get(MeilisearchInterface::class)->search(
            $orga->getCrmIndexName(),
            'olivie.gregoire@gmail.com'
        );

        $this->assertCount(0, $documents['hits']);

        // Check job status
        $job = static::getContainer()->get(JobRepository::class)->find($job->getId());
        $this->assertSame('remove_batch', $job->getType());
        $this->assertSame(1, $job->getStep());
        $this->assertSame(1, $job->getTotal());
        $this->assertSame(1.0, $job->getProgress());
        $this->assertTrue($job->isFinished());
        $this->assertSame([], $job->getPayload());
    }
}
