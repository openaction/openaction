<?php

namespace App\Tests\Controller\Cdn;

use App\Entity\Upload;
use App\Repository\ProjectRepository;
use App\Repository\UploadRepository;
use App\Tests\WebTestCase;

class ServeControllerTest extends WebTestCase
{
    public function provideCdnServe()
    {
        yield 'document.pdf' => [
            __DIR__.'/../../Fixtures/upload/document.pdf',
            'e816bcc6-0568-46d1-b0c5-917ce4810a87/document.pdf',
            'application/pdf',
        ];

        yield 'french.jpg' => [
            __DIR__.'/../../Fixtures/upload/french.jpg',
            'e816bcc6-0568-46d1-b0c5-917ce4810a87/french.jpg',
            'image/jpeg',
        ];

        yield 'mario.png' => [
            __DIR__.'/../../Fixtures/upload/mario.png',
            'e816bcc6-0568-46d1-b0c5-917ce4810a87/mario.png',
            'image/png',
        ];

        yield 'penguin.bmp' => [
            __DIR__.'/../../Fixtures/upload/penguin.bmp',
            'e816bcc6-0568-46d1-b0c5-917ce4810a87/penguin.bmp',
            'image/x-ms-bmp',
        ];
    }

    /**
     * @dataProvider provideCdnServe
     */
    public function testCdnServe(string $filename, string $path, string $expectedContentType)
    {
        $client = static::createClient();

        // Create CDN file
        static::getContainer()->get('cdn.storage')->write($path, file_get_contents($filename));

        // Create upload entity
        $this->assertNull(static::getContainer()->get(UploadRepository::class)->findUpload($path));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Citipo']);
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->persist(new Upload($path, $project));
        $em->flush();

        $upload = static::getContainer()->get(UploadRepository::class)->findUpload($path);
        $this->assertInstanceOf(Upload::class, $upload);

        // Try to access the content
        $client->request('GET', '/serve/'.$path);

        // Should succeed
        $this->assertResponseIsSuccessful();
        $this->assertSame($expectedContentType, $client->getResponse()->headers->get('Content-Type'));
        $this->assertSame('max-age=604800, public, s-maxage=604800', $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testCdnServeSharer()
    {
        $client = static::createClient();

        // Create CDN file
        static::getContainer()->get('cdn.storage')->write(
            'e816bcc6-0568-46d1-b0c5-917ce4810a87/mario.png',
            file_get_contents(__DIR__.'/../../Fixtures/upload/mario.png')
        );

        // Create upload entity
        $this->assertNull(static::getContainer()->get(UploadRepository::class)->findUpload('e816bcc6-0568-46d1-b0c5-917ce4810a87/mario.png'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Citipo']);
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->persist(new Upload('e816bcc6-0568-46d1-b0c5-917ce4810a87/mario.png', $project));
        $em->flush();

        $upload = static::getContainer()->get(UploadRepository::class)->findUpload('e816bcc6-0568-46d1-b0c5-917ce4810a87/mario.png');
        $this->assertInstanceOf(Upload::class, $upload);

        // Try to access the content
        $client->request('GET', '/serve/'.self::PROJECT_CITIPO_UUID.'/mario.png?t=sharer');

        // Should succeed
        $this->assertResponseIsSuccessful();
        $this->assertSame('image/jpeg', $client->getResponse()->headers->get('Content-Type'));
        $this->assertSame('max-age=604800, public, s-maxage=604800', $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testCdnServePrivate()
    {
        $client = static::createClient();

        $path = 'e816bcc6-0568-46d1-b0c5-917ce4810a87/private/french.jpg';

        // Create CDN file
        static::getContainer()->get('cdn.storage')->write($path, file_get_contents(__DIR__.'/../../Fixtures/upload/french.jpg'));

        // Create upload entity
        $this->assertNull(static::getContainer()->get(UploadRepository::class)->findUpload($path));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Citipo']);
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->persist(new Upload($path, $project));
        $em->flush();

        $upload = static::getContainer()->get(UploadRepository::class)->findUpload($path);
        $this->assertInstanceOf(Upload::class, $upload);

        // Try to access the content
        $client->request('GET', '/serve/'.$path);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCdnServeNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/serve/invalid.png');

        $this->assertResponseStatusCodeSame(404);
    }
}
