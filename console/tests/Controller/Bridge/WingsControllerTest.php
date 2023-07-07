<?php

namespace App\Tests\Controller\Bridge;

use App\Community\Webhook\WingsWebhookMessage;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use App\Util\Json;

class WingsControllerTest extends WebTestCase
{
    public function testWebhook()
    {
        $client = static::createClient();

        $content = file_get_contents(__DIR__.'/../../Fixtures/wings/signature_created.json');
        $client->request(
            'POST',
            '/webhook/wings/'.self::PROJECT_CITIPO_UUID.'?t=748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad',
            server: ['HTTP_ACCEPT' => 'application/ld+json'],
            content: $content,
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(1, $messages = $transport->get());

        /* @var WingsWebhookMessage $message */
        $this->assertInstanceOf(WingsWebhookMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame(Json::decode($content), $message->getPayload());

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $this->assertSame($project->getId(), $message->getProjectId());
    }

    public function testNoToken()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/webhook/wings/'.self::PROJECT_CITIPO_UUID,
            server: ['HTTP_ACCEPT' => 'application/ld+json'],
            content: Json::encode([]),
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidToken()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/webhook/wings/'.self::PROJECT_CITIPO_UUID.'?t=invalid',
            server: ['HTTP_ACCEPT' => 'application/ld+json'],
            content: Json::encode([]),
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidProject()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/webhook/wings/invalid?t=invalid',
            server: ['HTTP_ACCEPT' => 'application/ld+json'],
            content: Json::encode([]),
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidJson()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/webhook/wings/'.self::PROJECT_CITIPO_UUID.'?t=748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad',
            server: ['HTTP_ACCEPT' => 'application/ld+json'],
            content: 'invalid',
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
