<?php

namespace App\Tests\Controller\Console\User;

use App\Repository\UserRepository;
use App\Tests\WebTestCase;

class NotificationSettingsControllerTest extends WebTestCase
{
    public function provideNotificationSettings(): iterable
    {
        yield 'full' => [
            'titouan.galopin@citipo.com',
            [
                'project_created',
                'newsletter_sent',
                'low_credits',
                'feature_added',
            ],
        ];
        yield 'empty' => [
            'titouan.galopin@citipo.com',
            [
                false,
                false,
                false,
                false,
            ],
        ];
        yield 'half' => [
            'titouan.galopin@citipo.com',
            [
                'project_created',
                false,
                false,
                'feature_added',
            ],
        ];
    }

    /**
     * @dataProvider provideNotificationSettings
     */
    public function testChangeNotificationSettings(string $email, array $events): void
    {
        $client = static::createClient();
        $this->authenticate($client, $email);

        $crawler = $client->request('GET', '/console/user/notification/update');
        $this->assertSelectorExists('h3:contains("Notifications")');

        $button = $crawler->selectButton('Update');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'notification_settings[events]' => $events,
        ]);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $this->assertEqualsCanonicalizing(
            array_filter($events),
            $user->getNotificationSettings()->toArray()
        );
    }
}
