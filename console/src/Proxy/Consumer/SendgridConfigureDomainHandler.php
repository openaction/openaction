<?php

namespace App\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\SendgridDomainConfig;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Add a domain to Sendgrid.
 */
final class SendgridConfigureDomainHandler implements MessageHandlerInterface
{
    use DomainHandlerTrait;

    public function __invoke(SendgridConfigureDomainMessage $message)
    {
        if (!$domain = $this->findDomain($message->getDomainId())) {
            return;
        }

        // Ignore already configured domains
        if ($domain->getConfigurationStatus()['sendgrid_ready'] ?? false) {
            return;
        }

        $this->doConfigure($domain, $domain->getSendgridConfig());

        $this->manager->persist($domain);
        $this->manager->flush();
    }

    private function doConfigure(Domain $domain, SendgridDomainConfig $config)
    {
        // Create domain if necessary
        if ($this->workflow->can($domain, 'sendgrid_domain_create')) {
            $this->createDomain($domain, $config);
            $domain->setSendgridConfig($config);

            $this->workflow->apply($domain, 'sendgrid_domain_create');

            return;
        }

        // Create branded link if necessary;
        if ($this->workflow->can($domain, 'sendgrid_branded_link_create')) {
            $this->createBrandedLink($domain, $config);
            $domain->setSendgridConfig($config);

            $this->workflow->apply($domain, 'sendgrid_branded_link_create');

            return;
        }

        // Synchronize the Sendgrid config if it's still partial
        if (!$config->isReadyToProvision()) {
            if ($config->getId()) {
                $this->loadSendgridConfig($config);
                $domain->setSendgridConfig($config);
            }

            return;
        }

        // Finally if everything is ready, provision
        $zoneId = $domain->getCloudflareConfig()->getId();
        foreach ($config->getCnameRecords() as $record) {
            $this->cloudflare->createRootDomainCname($zoneId, $record['host'], $record['target']);
        }

        $this->workflow->apply($domain, 'sendgrid_provision');

        // Check on Cloudflare
        $this->bus->dispatch(new SendgridCheckDomainMessage($domain->getId()));
    }

    private function createDomain(Domain $domain, SendgridDomainConfig $config)
    {
        // A domain already exists, store it in database
        if ($configs = $this->sendgrid->findDomainsByName($domain->getName())) {
            // Use the first one
            $domainConfig = array_shift($configs);
            if (null === ($domainConfig['id'] ?? null)) {
                throw new \RuntimeException(sprintf(
                    'Sendgrid returned a domain without id for "%s".',
                    $domain->getName()
                ));
            }
            $config->setId($domainConfig['id']);

            // Remove the others
            foreach ($configs as $d) {
                $this->sendgrid->removeDomain($d['id']);
            }

            return;
        }

        // No domain exists, create it
        $config->setId($this->sendgrid->createDomain($domain->getName()));
    }

    private function createBrandedLink(Domain $domain, SendgridDomainConfig $config)
    {
        // A domain already exists, store it in database
        if ($configs = $this->sendgrid->findBrandedLinksByName($domain->getName())) {
            // Use the first one
            $brandedLinkConfig = array_shift($configs);
            $config->setBrandedLinkId($brandedLinkConfig['id']);

            // Remove the others
            foreach ($configs as $d) {
                $this->sendgrid->removeBrandedLink($d['id']);
            }

            return;
        }

        // No branded link exists, create it
        $config->setBrandedLinkId($this->sendgrid->createBrandedLink($domain->getName()));
    }

    private function loadSendgridConfig(SendgridDomainConfig $config)
    {
        // If the domain is missing details on how to provision, sync
        if (!$config->isDomainReadyToProvision()) {
            $config->updateDomainConfig($this->sendgrid->getDomainConfig($config->getId()));
        }

        // If the branded link is missing details on how to provision, sync
        if (!$config->isBrandedLinkReadyToProvision()) {
            $config->updateBrandedLinkConfig($this->sendgrid->getBrandedLinkConfig($config->getBrandedLinkId()));
        }
    }
}
