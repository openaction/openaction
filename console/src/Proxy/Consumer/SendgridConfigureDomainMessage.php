<?php

namespace App\Proxy\Consumer;

final class SendgridConfigureDomainMessage
{
    private int $domainId;

    public function __construct(int $domainId)
    {
        $this->domainId = $domainId;
    }

    public function getDomainId(): int
    {
        return $this->domainId;
    }
}
