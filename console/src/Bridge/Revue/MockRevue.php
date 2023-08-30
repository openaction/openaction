<?php

namespace App\Bridge\Revue;

class MockRevue implements RevueInterface
{
    public function getSubscribers(string $apiToken): array
    {
        return [
            [
                'id' => 311518323,
                'list_id' => 315790,
                'email' => 'revue.subscriber@gmail.com',
                'first_name' => 'Revue',
                'last_name' => 'Subscriber',
                'last_changed' => '2021-09-20T18:13:35.830Z',
            ],
        ];
    }
}
