<?php

namespace App\Bridge\Cloudflare;

use App\Entity\Model\CloudflareDomainConfig;

class MockCloudflare implements CloudflareInterface
{
    public array $domains = [];
    public array $provisioned = [];
    public array $checked = [];
    public array $records = [];

    public function isEnabled(): bool
    {
        return true;
    }

    public function createRootDomain(string $domain): CloudflareDomainConfig
    {
        $this->domains[$domain] = true;

        return new CloudflareDomainConfig('1', $domain, 'active', []);
    }

    public function provisionRootDomain(string $zoneId): CloudflareDomainConfig
    {
        $this->provisioned[$zoneId] = true;

        return new CloudflareDomainConfig('1', $zoneId.'.com', 'active', []);
    }

    public function getRootDomainConfig(string $zoneId): CloudflareDomainConfig
    {
        $this->checked[$zoneId] = true;

        return new CloudflareDomainConfig('1', $zoneId.'.com', 'active', []);
    }

    public function createRootDomainTxt(string $zoneId, string $host, string $content): bool
    {
        $this->records[$zoneId][$host][] = $content;

        return true;
    }

    public function createRootDomainCname(string $zoneId, string $host, string $target, bool $dnsOnly = true): bool
    {
        $this->records[$zoneId][$host][] = $target;

        return true;
    }

    public function getAllTrialSubdomains(): array
    {
        return array_keys($this->domains);
    }

    public function hasTrialSubdomain(string $subdomain): bool
    {
        return isset($this->domains[$subdomain]);
    }

    public function createTrialSubdomain(string $subdomain): bool
    {
        $this->domains[$subdomain] = true;

        return true;
    }


    public function removeTrialSubdomain(string $recordId): void
    {
        unset($this->domains[$subdomain]);
    }
}
