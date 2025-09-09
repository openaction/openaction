<?php

namespace App\Proxy\Consumer;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Check a domain's status on Sendgrid.
 */
#[AsMessageHandler]
final class SendgridCheckDomainHandler
{
    use DomainHandlerTrait;

    public function __invoke(SendgridCheckDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        // Ignore already configured domains
        if ($domain->getConfigurationStatus()['sendgrid_ready'] ?? false) {
            return;
        }

        $config = $domain->getSendgridConfig();

        // If the domain wasn't provisioned, provision it
        if (!$this->workflow->can($domain, 'sendgrid_active')) {
            $this->bus->dispatch(new SendgridConfigureDomainMessage($domain->getId()));

            return;
        }

        // Domain isn't currently valid
        if (!$config->isDomainAuthValid()) {
            // Sync
            $config->updateDomainConfig($this->sendgrid->getDomainConfig($config->getId()));
            $domain->setSendgridConfig($config);

            // Still not valid
            if (!$config->isDomainAuthValid()) {
                $this->sendgrid->requestDomainConfigValidation($config->getId());
            }
        }

        // Branded link isn't currently valid
        if (!$config->isBrandedLinkValid()) {
            // Sync
            $config->updateBrandedLinkConfig($this->sendgrid->getBrandedLinkConfig($config->getBrandedLinkId()));
            $domain->setSendgridConfig($config);

            // Still not valid
            if (!$config->isBrandedLinkValid()) {
                $this->sendgrid->requestBrandedLinkConfigValidation($config->getBrandedLinkId());
            }
        }

        // If fully configured, apply workflow
        if ($config->isFullyConfigured()) {
            $this->workflow->apply($domain, 'sendgrid_active');
        }

        $this->manager->persist($domain);
        $this->manager->flush();
    }
}
