<?php

namespace App\Bridge\Twilio;

use Twilio\Rest\Api\V2010\Account\MessageInstance;

class MockTwilio implements TwilioInterface
{
    public array $messages = [];

    public function verifySignature(array $payload, string $signature, string $uri): bool
    {
        return true;
    }

    public function sendMessage(?string $from, string $to, string $body, ?string $statusCallbackUrl = null): MessageInstance
    {
        $this->messages[] = [
            'from' => $from,
            'to' => $to,
            'body' => $body,
            'statusCallbackUrl' => $statusCallbackUrl,
        ];

        /** @var MessageInstance $model */
        $model = (new \ReflectionClass(MessageInstance::class))->newInstanceWithoutConstructor();

        return $model;
    }
}
