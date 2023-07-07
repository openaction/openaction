<?php

namespace App\Bridge\Postmark;

use App\Entity\Model\PostmarkDomainConfig;
use Postmark\PostmarkAdminClient;

class Postmark implements PostmarkInterface
{
    private PostmarkAdminClient $client;

    public function __construct(string $accountToken)
    {
        $this->client = new PostmarkAdminClient($accountToken);
    }

    public function authenticateRootDomain(string $domain): PostmarkDomainConfig
    {
        return $this->mapToConfig($this->client->createDomain($domain, 'pm-bounces.'.$domain));
    }

    public function getRootDomainConfig(string $domainId): PostmarkDomainConfig
    {
        return $this->mapToConfig($this->client->getDomain($domainId));
    }

    private function mapToConfig(object $data): PostmarkDomainConfig
    {
        return new PostmarkDomainConfig(
            $data->id,
            $data->name,
            $data->dkimverified,
            $data->dkimhost ?: $data->dkimpendinghost ?: $data->dkimrevokedhost,
            $data->dkimtextvalue ?: $data->dkimpendingtextvalue ?: $data->dkimrevokedtextvalue,
            $data->returnpathdomainverified,
            $data->returnpathdomain,
            $data->returnpathdomaincnamevalue,
        );
    }
}
