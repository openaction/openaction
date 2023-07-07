<?php

namespace App\Bridge\Uploadcare\Model;

class UploadKey
{
    public function __construct(private string $publicKey, private string $signature, private int $expire)
    {
    }

    public function toArray(): array
    {
        return [
            'publicKey' => $this->publicKey,
            'signature' => $this->signature,
            'expire' => $this->expire,
        ];
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getExpire(): int
    {
        return $this->expire;
    }
}
