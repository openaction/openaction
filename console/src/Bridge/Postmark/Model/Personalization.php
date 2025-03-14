<?php

namespace App\Bridge\Postmark\Model;

class Personalization
{
    public function __construct(
        public readonly string $to,
        public readonly array $metadata,
        public readonly string $htmlContent,
    ) {
    }
}
