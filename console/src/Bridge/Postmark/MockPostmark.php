<?php

namespace App\Bridge\Postmark;

use App\Entity\Model\PostmarkDomainConfig;

class MockPostmark implements PostmarkInterface
{
    public array $authenticated = [];
    public array $checked = [];

    public function authenticateRootDomain(string $domain): PostmarkDomainConfig
    {
        $this->authenticated[$domain] = true;

        return new PostmarkDomainConfig(
            1,
            $domain,
            false,
            '20210726220429pm._domainkey.'.$domain,
            'k=rsa; p=MI',
            false,
            'pm-bounces.'.$domain,
            'pm.mtasv.net',
        );
    }

    public function getRootDomainConfig(string $domainId): PostmarkDomainConfig
    {
        $this->checked[$domainId] = true;

        return new PostmarkDomainConfig(
            $domainId,
            'domain.com',
            true,
            '20210726220429pm._domainkey.domain.com',
            'k=rsa; p=MI',
            true,
            'pm-bounces.domain.com',
            'pm.mtasv.net',
        );
    }
}
