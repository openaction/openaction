<?php

namespace App\Proxy\Consumer;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Check a domain's status on Postmark.
 */
#[AsMessageHandler]
final class PostmarkCheckDomainHandler
{
    use DomainHandlerTrait;

    public function __invoke(PostmarkCheckDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        if (!$this->workflow->can($domain, 'postmark_configure')) {
            return;
        }

        // If the configuration didn't work, try it again
        if (!$domain->getPostmarkConfig()->getId()) {
            $this->bus->dispatch(new PostmarkConfigureDomainMessage($domain->getId()));

            return;
        }

        $config = $this->postmark->getRootDomainConfig($domain->getPostmarkConfig()->getId());
        $domain->setPostmarkConfig($config);

        // If the status is ready, apply workflow
        if ($config->isFullyVerified()) {
            $this->workflow->apply($domain, 'postmark_configure');
        }

        $this->manager->persist($domain);
        $this->manager->flush();
    }
}
