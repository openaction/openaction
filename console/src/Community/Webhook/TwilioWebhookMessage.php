<?php

namespace App\Community\Webhook;

use Symfony\Component\HttpFoundation\Request;

class TwilioWebhookMessage
{
    private string $messageId;
    private array $payload;
    private string $signature;
    private string $uri;

    public function __construct(string $messageId, array $payload, string $signature, string $uri)
    {
        $this->messageId = $messageId;
        $this->payload = $payload;
        $this->signature = $signature;
        $this->uri = $uri;
    }

    public static function fromRequest(string $messageId, Request $request): self
    {
        return new self(
            $messageId,
            $request->request->all(),
            $request->headers->get('X-Twilio-Signature', ''),
            str_replace('http://', 'https://', $request->getUri()),
        );
    }

    public function toArray(): array
    {
        return [
            'messageId' => $this->messageId,
            'payload' => $this->payload,
            'signature' => $this->signature,
            'uri' => $this->uri,
        ];
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
