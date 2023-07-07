<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Upload;
use App\Entity\Website\Document;
use App\Repository\ProjectRepository;
use App\Repository\UploadRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class DocumentControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('[data-document]'));
    }

    public function provideListOrder()
    {
        yield [['file2.pdf', 'file1.pdf']];
    }

    /**
     * @dataProvider provideListOrder
     */
    public function testListOrder(array $documentsName)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents');
        foreach ($crawler->filter('[data-document]') as $index => $li) {
            $this->assertStringContainsString($documentsName[$index], $li->nodeValue);
        }
    }

    public function provideCreateValid(): iterable
    {
        yield 'document.pdf' => ['document.pdf', 'd.pdf', 'application/pdf'];
        yield 'mario.png' => ['mario.png', 'm.png', 'image/png'];
    }

    /**
     * @dataProvider provideCreateValid
     */
    public function testCreateValid(string $filename, string $name, string $mimetype)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents');
        $this->assertResponseIsSuccessful();

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/'.$filename, $name, $mimetype, null, true);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents/create',
            [],
            ['document' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($payload = $client->getResponse()->getContent());
        $this->assertTrue(Json::decode($payload)['success']);
    }

    public function provideCreateInvalidData(): iterable
    {
        yield 'invalid_file_large' => ['large.pdf'];
        yield 'invalid_file_format' => ['penguin.bmp', 'image/bmp'];
    }

    /**
     * @dataProvider provideCreateInvalidData
     */
    public function testCreateInvalidData(string $filename, $mimetype = 'application/pdf')
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents');
        $this->assertResponseIsSuccessful();

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/'.$filename, $filename, $mimetype, null, true);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents/create',
            [],
            ['document' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJson($payload = $client->getResponse()->getContent());
        $this->assertSame(Json::decode($payload)['status'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('[data-document]'));

        $client->clickLink('Delete');
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-document]'));
    }

    public function provideContent()
    {
        yield 'document.pdf' => [
            __DIR__.'/../../../../Fixtures/upload/document.pdf',
            'e816bcc6-0568-46d1-b0c5-917ce4810a87/document/document.pdf',
            'application/pdf',
        ];
    }

    /**
     * @dataProvider provideContent
     */
    public function testDownload(string $filename, string $path, string $mimeType)
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Create CDN file
        static::getContainer()->get('cdn.storage')->write($path, file_get_contents($filename));

        // Create upload entity
        $this->assertNull(static::getContainer()->get(UploadRepository::class)->findUpload($path));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Citipo']);
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $upload = new Upload($path, $project);
        $em->persist($upload);
        $em->flush();

        // Create Document
        $document = new Document($project, 'wellknownname.pdf', $upload);
        $em->persist($document);
        $em->flush();

        // Check file is downloaded properly
        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents/'.$document->getUuid().'/download');
        $this->assertEquals($mimeType, $client->getInternalResponse()->getHeader('Content-Type'));
        $this->assertEquals('attachment; filename='.$document->getName(), $client->getInternalResponse()->getHeader('Content-Disposition'));
        $this->assertStringEqualsFile($filename, $client->getInternalResponse()->getContent());

        // Check if file is in the list and we have a download link
        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents');
        $this->assertSelectorExists('td:contains("'.$document->getName().'")');
        $this->assertSelectorExists('a[href="/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents/'.$document->getUuid().'/download"]');
    }
}
