<?php

namespace App\Proxy\Consumer;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Provision records for a domain on Cloudflare.
 */
#[AsMessageHandler]
final class CloudflareProvisionDomainHandler
{
    use DomainHandlerTrait;

    public function __invoke(CloudflareProvisionDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        if (!$this->workflow->can($domain, 'cloudflare_provision')) {
            return;
        }

        $domain->setCloudflareConfig($this->cloudflare->provisionRootDomain($domain->getCloudflareConfig()->getId()));
        $this->workflow->apply($domain, 'cloudflare_provision');

        $this->manager->persist($domain);
        $this->manager->flush();
    }
}
