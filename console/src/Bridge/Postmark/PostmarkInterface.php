<?php

namespace App\Bridge\Postmark;

use App\Entity\Model\PostmarkDomainConfig;

interface PostmarkInterface
{
    public function authenticateRootDomain(string $domain): PostmarkDomainConfig;

    public function getRootDomainConfig(string $domainId): PostmarkDomainConfig;
}
