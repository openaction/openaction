<?php

namespace App\Tests\Search\Consumer;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Entity\Platform\Job;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Repository\Platform\JobRepository;
use App\Search\Consumer\RemoveTagCrmBatchHandler;
use App\Search\Consumer\RemoveTagCrmBatchMessage;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RemoveTagCrmBatchHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(RemoveTagCrmBatchHandler::class);
        $this->assertTrue($handler(new RemoveTagCrmBatchMessage(0, 0, [], 0)));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');

        /** @var Job $job */
        $job = static::getContainer()->get(JobRepository::class)->startJob('remove_tag_batch', 0, 1);
        $this->assertSame('remove_tag_batch', $job->getType());
        $this->assertSame(0, $job->getStep());
        $this->assertSame(1, $job->getTotal());
        $this->assertSame(0.0, $job->getProgress());
        $this->assertFalse($job->isFinished());
        $this->assertSame([], $job->getPayload());

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'StartWithTag']);

        $handler = static::getContainer()->get(RemoveTagCrmBatchHandler::class);
        $this->assertTrue($handler(new RemoveTagCrmBatchMessage(
            $job->getId(),
            $orga->getId(),
            [
                'queryInput' => 'bru',
                'queryFilter' => ['status = \'m\''],
                'querySort' => ['profile_first_name:desc'],
                'params' => ['tagId' => $tag->getId()],
            ],
            $tag->getId()
        )));

        // Clear relationships to avoid false-issues with tags
        static::getContainer()->get(EntityManagerInterface::class)->clear();

        // Check database is updated
        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'brunella.courtemanche2@orange.fr']);
        $this->assertNotContains('StartWithTag', $contact->getMetadataTagsNames());

        // Check Meilisearch is updated
        $documents = static::getContainer()->get(MeilisearchInterface::class)->search(
            $orga->getCrmIndexName(),
            'brunella.courtemanche2@orange.fr'
        );

        $this->assertCount(1, $hits = $documents['hits']);
        $this->assertNotContains($tag->getId(), $hits[0]['tags']);
        $this->assertNotContains('StartWithTag', $hits[0]['tags_names']);

        // Check job status
        $job = static::getContainer()->get(JobRepository::class)->find($job->getId());
        $this->assertSame('remove_tag_batch', $job->getType());
        $this->assertSame(1, $job->getStep());
        $this->assertSame(1, $job->getTotal());
        $this->assertSame(1.0, $job->getProgress());
        $this->assertTrue($job->isFinished());
        $this->assertSame([], $job->getPayload());
    }
}
