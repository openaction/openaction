<?php

namespace App\Tests\Controller\Bridge;

use App\Tests\WebTestCase;
use App\Theme\Consumer\SyncThemeMessage;

class GithubThemeControllerTest extends WebTestCase
{
    public function testEventWebsite()
    {
        $headers = [
            'HTTP_X_GITHUB_EVENT' => 'push',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=6019e08b6aba8a933f0124d14f0f7d3283b415aa79ee2717e23fae06792f6f67',
        ];

        $content = trim(file_get_contents(__DIR__.'/../../Fixtures/github_website_themes/push_main.json'));

        $client = static::createClient();
        $client->request('POST', '/webhook/github/theme/event/website', [], [], $headers, $content);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(SyncThemeMessage::class, $messages[0]->getMessage());
    }
}
