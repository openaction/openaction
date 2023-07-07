<?php

namespace App\Community\Webhook;

use SendGrid\EventWebhook\EventWebhookHeader;
use Symfony\Component\HttpFoundation\Request;

class SendgridWebhookMessage
{
    private string $content;
    private string $signature;
    private string $timestamp;

    public function __construct(string $content, string $signature, string $timestamp)
    {
        $this->content = $content;
        $this->signature = $signature;
        $this->timestamp = $timestamp;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->getContent(),
            $request->headers->get(EventWebhookHeader::SIGNATURE, ''),
            $request->headers->get(EventWebhookHeader::TIMESTAMP, '')
        );
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'signature' => $this->signature,
            'timestamp' => $this->timestamp,
        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }
}
