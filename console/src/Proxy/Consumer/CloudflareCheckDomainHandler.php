<?php

namespace App\Proxy\Consumer;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Check the status of a domain on Cloudflare and triggers additional configuration when it's ready.
 */
final class CloudflareCheckDomainHandler implements MessageHandlerInterface
{
    use DomainHandlerTrait;

    public function __invoke(CloudflareCheckDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        if (!$this->workflow->can($domain, 'cloudflare_active')) {
            return;
        }

        $config = $this->cloudflare->getRootDomainConfig($domain->getCloudflareConfig()->getId());
        $domain->setCloudflareConfig($config);

        // If the status is ready, persists and then triggers additional configuration
        if ($config->isActive()) {
            $this->workflow->apply($domain, 'cloudflare_active');
        }

        $this->manager->persist($domain);
        $this->manager->flush();

        if ($config->isActive()) {
            $this->bus->dispatch(new SendgridConfigureDomainMessage($domain->getId()));
            $this->bus->dispatch(new PostmarkConfigureDomainMessage($domain->getId()));
        }
    }
}
