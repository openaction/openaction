<?php

namespace App\Proxy\Consumer;

use Postmark\Models\PostmarkException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Add a domain to Postmark.
 */
final class PostmarkConfigureDomainHandler implements MessageHandlerInterface
{
    use DomainHandlerTrait;

    public function __invoke(PostmarkConfigureDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        if (!$this->workflow->can($domain, 'postmark_configure')) {
            return;
        }

        // Request authentication config
        try {
            $config = $this->postmark->authenticateRootDomain($domain->getName());
        } catch (PostmarkException $e) {
            // Ignore already created domains
            if (str_contains($e->getMessage(), 'Domain already exists')) {
                return;
            }
        }

        $domain->setPostmarkConfig($config);

        $this->manager->persist($domain);
        $this->manager->flush();

        // Provision on Cloudflare
        $zoneId = $domain->getCloudflareConfig()->getId();
        $this->cloudflare->createRootDomainTxt($zoneId, $config->getDkimHost(), $config->getDkimContent());
        $this->cloudflare->createRootDomainCname($zoneId, $config->getReturnPathHost(), $config->getReturnPathTarget());

        // Check on Cloudflare
        $this->bus->dispatch(new PostmarkCheckDomainMessage($domain->getId()));
    }
}
