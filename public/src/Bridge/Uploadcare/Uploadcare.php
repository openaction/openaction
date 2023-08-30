<?php

namespace App\Bridge\Uploadcare;

use App\Bridge\Uploadcare\Model\UploadKey;

class Uploadcare
{
    public function __construct(private string $publicKey, private string $secretKey)
    {
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function generateUploadKey(): UploadKey
    {
        // Expires after 15 minutes
        return new UploadKey(hash_hmac('sha256', time() + 900, $this->secretKey), time() + 900);
    }
}
