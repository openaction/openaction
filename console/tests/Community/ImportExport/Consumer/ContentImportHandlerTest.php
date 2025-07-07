<?php

namespace App\Tests\Community\ImportExport\Consumer;

use App\Entity\Community\ContentImport;
use App\Entity\Community\Model\ContentImportSettings;
use App\Entity\Upload;
use App\Entity\Website\Page;
use App\Entity\Website\Post;
use App\Repository\Community\ContentImportRepository;
use App\Repository\Website\PageRepository;
use App\Repository\Website\PostRepository;
use App\Tests\KernelTestCase;
use App\Website\ImportExport\Consumer\ContentImportHandler;
use App\Website\ImportExport\Consumer\ContentImportMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class ContentImportHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid(): void
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ContentImportHandler::class);
        $handler(new ContentImportMessage(0));

        /** @var ReceiverInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsumeValid(): void
    {
        self::bootKernel();

        /** @var ContentImport $import */
        $import = static::getContainer()->get(ContentImportRepository::class)->findOneByUuid('8a7f9d2e-56c1-4826-9b40-7fe8a58e3d14');
        $this->assertInstanceOf(ContentImport::class, $import);

        $job = $import->getJob();
        $this->assertFalse($job->isFinished());
        $this->assertSame(0, $job->getTotal());

        static::getContainer()->get('cdn.storage')->write('import-started.xml', file_get_contents(__DIR__.'/../../../Fixtures/import/import-content-wordpress-example-file.xml'));

        $handler = static::getContainer()->get(ContentImportHandler::class);
        $handler(new ContentImportMessage($import->getId()));

        // check imported unpublished blogpost
        /** @var Post $post */
        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['slug' => 'test-unpublished-blog-post-the-journey-begins']);
        $this->assertSame('Test unpublished blog post: The Journey Begins', $post->getTitle());
        $this->assertStringContainsString('Let the journey begin!', $post->getDescription());
        $this->assertStringContainsString('Izaak Walton', $post->getContent());
        $this->assertSame('2018-10-08', $post->getCreatedAt()->format('Y-m-d'));
        $this->assertNull($post->getPublishedAt()); // unpublished (draft)
        $this->assertNull($post->getImage());
        $this->assertSame('e816bcc6-0568-46d1-b0c5-917ce4810a87', (string) $post->getProject()->getUuid()); // correct project
        $this->assertSame('https://renaissanceforeurope.wordpress.com/the-journey-begins/', $post->getImportedUrl());

        // check imported published blogpost
        /** @var Post $post */
        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['slug' => 'test-published-blog-post-our-pledge']);
        $this->assertSame('Test published blog post: Our Pledge', $post->getTitle());
        $this->assertStringContainsString('This is our pledge', $post->getDescription());
        $this->assertStringContainsString('In varietate concordia', $post->getContent());
        $this->assertSame('2018-10-09', $post->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('2018-10-09', $post->getPublishedAt()->format('Y-m-d')); // published
        $this->assertNull($post->getImage());
        $this->assertSame('e816bcc6-0568-46d1-b0c5-917ce4810a87', (string) $post->getProject()->getUuid()); // correct project
        $this->assertSame('https://renaissanceforeurope.wordpress.com/?page_id=57', $post->getImportedUrl());

        // check the imported page
        /** @var Page $page */
        $page = static::getContainer()->get(PageRepository::class)->findOneBy(['slug' => 'test-published-page-why-a-european-renaissance']);
        $this->assertSame('Test published page: Why a European Renaissance ?', $page->getTitle());
        $this->assertNull($page->getDescription());
        $this->assertStringContainsString('This is our purpose', $page->getContent());
        $this->assertSame('2018-10-08', $page->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('e816bcc6-0568-46d1-b0c5-917ce4810a87', (string) $page->getProject()->getUuid()); // correct project

        // check attachment (image)
        $attachment = $page->getImage();
        $this->assertNotNull($attachment); // check if image successfully imported
        $this->assertInstanceOf(Upload::class, $attachment);
        $this->assertStringContainsString('website-content', $attachment->getPathname());
        $this->assertContains($attachment->getExtension(), ContentImportSettings::ALLOWED_IMAGE_EXTENSIONS);

        // check if jobs is processed
        static::getContainer()->get(EntityManagerInterface::class)->refresh($job);
        $this->assertSame(3, $job->getTotal());
        $this->assertTrue($job->isFinished());
    }
}
