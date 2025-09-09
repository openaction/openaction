<?php

namespace App\Bridge\Twilio;

use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
use Twilio\Security\RequestValidator;

class Twilio implements TwilioInterface
{
    private string $token;
    private string $accountId;
    private string $defaultFrom;

    private ?Client $client = null;
    private ?RequestValidator $requestValidator = null;

    public function __construct(string $token, string $accountId, string $defaultFrom)
    {
        $this->token = $token;
        $this->accountId = $accountId;
        $this->defaultFrom = $defaultFrom;
    }

    public function verifySignature(array $payload, string $signature, string $uri): bool
    {
        if (!$this->requestValidator) {
            $this->requestValidator = new RequestValidator($this->token);
        }

        return $this->requestValidator->validate($signature, $uri, $payload);
    }

    public function sendMessage(?string $from, string $to, string $body, ?string $statusCallbackUrl = null): MessageInstance
    {
        if (!$this->client) {
            $this->client = new Client($this->accountId, $this->token);
        }

        $payload = ['from' => $from ?: $this->defaultFrom, 'body' => $body."\n\nSTOP SMS"];
        if ($statusCallbackUrl) {
            $payload['statusCallback'] = $statusCallbackUrl;
        }

        return $this->client->messages->create($to, $payload);
    }
}
