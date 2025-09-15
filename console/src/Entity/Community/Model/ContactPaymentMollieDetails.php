<?php

namespace App\Entity\Community\Model;

class ContactPaymentMollieDetails
{
    public string $transactionId;

    /**
     * Raw provider payload as received from Mollie (order/payment/customer...), unserialized.
     */
    public array $rawPayload;

    public function __construct(string $transactionId, array $rawPayload)
    {
        $this->transactionId = $transactionId;
        $this->rawPayload = $rawPayload;
    }
}
