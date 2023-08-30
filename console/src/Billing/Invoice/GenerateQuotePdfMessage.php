<?php

namespace App\Billing\Invoice;

final class GenerateQuotePdfMessage
{
    private int $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function getQuoteId(): int
    {
        return $this->quoteId;
    }
}
