<?php

namespace App\Bridge\Twilio;

use Twilio\Rest\Api\V2010\Account\MessageInstance;

interface TwilioInterface
{
    public function verifySignature(array $payload, string $signature, string $uri): bool;

    public function sendMessage(?string $from, string $to, string $body, string $statusCallbackUrl = null): MessageInstance;
}
