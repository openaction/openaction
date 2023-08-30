<?php

namespace App\Proxy\Consumer;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Create a domain on Cloudflare.
 */
final class CloudflareCreateDomainHandler implements MessageHandlerInterface
{
    use DomainHandlerTrait;

    public function __invoke(CloudflareCreateDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        if (!$this->workflow->can($domain, 'cloudflare_create')) {
            return;
        }

        $domain->setCloudflareConfig($this->cloudflare->createRootDomain($domain->getName()));
        $this->workflow->apply($domain, 'cloudflare_create');

        $this->manager->persist($domain);
        $this->manager->flush();

        $this->bus->dispatch(new CloudflareProvisionDomainMessage($domain->getId()));
    }
}
