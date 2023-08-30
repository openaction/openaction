<?php

namespace App\Proxy\Consumer;

final class ConfigureTrialSubdomainMessage
{
    private string $subdomain;

    public function __construct(string $subdomain)
    {
        $this->subdomain = $subdomain;
    }

    public function getSubdomain(): string
    {
        return $this->subdomain;
    }
}
