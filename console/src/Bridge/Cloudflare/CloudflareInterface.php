<?php

namespace App\Bridge\Cloudflare;

use App\Entity\Model\CloudflareDomainConfig;

interface CloudflareInterface
{
    public function isEnabled(): bool;

    public function createRootDomain(string $domain): CloudflareDomainConfig;

    public function provisionRootDomain(string $zoneId): CloudflareDomainConfig;

    public function createRootDomainTxt(string $zoneId, string $host, string $content): bool;

    public function createRootDomainCname(string $zoneId, string $host, string $target, bool $dnsOnly = true): bool;

    public function getRootDomainConfig(string $zoneId): ?CloudflareDomainConfig;

    public function hasTrialSubdomain(string $subdomain): bool;

    public function createTrialSubdomain(string $subdomain): bool;
}
