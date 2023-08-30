<?php

namespace App\Bridge\Uploadcare\Model;

class UploadKey
{
    public function __construct(private string $signature, private int $expire)
    {
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
