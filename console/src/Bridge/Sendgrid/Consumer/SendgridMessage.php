<?php

namespace App\Bridge\Sendgrid\Consumer;

final class SendgridMessage
{
    public function __construct(
        public readonly int $batchId,
    ) {
    }
}
