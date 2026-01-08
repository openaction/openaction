<?php

namespace App\Bridge\Postmark\Consumer;

final class PostmarkMessage
{
    public function __construct(
        public readonly int $batchId,
    ) {
    }
}
