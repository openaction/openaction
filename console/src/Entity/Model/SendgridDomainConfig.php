<?php

namespace App\Entity\Model;

class SendgridDomainConfig
{
    private ?int $id = null;
    private ?string $domain = null;
    private ?string $subdomain = null;
    private ?bool $valid = null;
    private ?string $mailCnameHost = null;
    private ?string $mailCnameTarget = null;
    private ?string $dkim1CnameHost = null;
    private ?string $dkim1CnameTarget = null;
    private ?string $dkim2CnameHost = null;
    private ?string $dkim2CnameTarget = null;

    private ?int $brandedLinkId = null;
    private ?bool $brandedLinkValid = null;
    private ?string $brandedLinkDomainCnameHost = null;
    private ?string $brandedLinkDomainCnameTarget = null;
    private ?string $brandedLinkOwnerCnameHost = null;
    private ?string $brandedLinkOwnerCnameTarget = null;

    public static function fromConfig(array $config): self
    {
        $self = new self();
        $self->id = $config['id'] ?? null;
        $self->domain = $config['domain'] ?? null;
        $self->subdomain = $config['subdomain'] ?? null;
        $self->valid = $config['valid'] ?? null;
        $self->mailCnameHost = $config['mailCnameHost'] ?? null;
        $self->mailCnameTarget = $config['mailCnameTarget'] ?? null;
        $self->dkim1CnameHost = $config['dkim1CnameHost'] ?? null;
        $self->dkim1CnameTarget = $config['dkim1CnameTarget'] ?? null;
        $self->dkim2CnameHost = $config['dkim2CnameHost'] ?? null;
        $self->dkim2CnameTarget = $config['dkim2CnameTarget'] ?? null;
        $self->brandedLinkId = $config['brandedLinkId'] ?? null;
        $self->brandedLinkValid = $config['brandedLinkValid'] ?? null;
        $self->brandedLinkDomainCnameHost = $config['brandedLinkDomainCnameHost'] ?? null;
        $self->brandedLinkDomainCnameTarget = $config['brandedLinkDomainCnameTarget'] ?? null;
        $self->brandedLinkOwnerCnameHost = $config['brandedLinkOwnerCnameHost'] ?? null;
        $self->brandedLinkOwnerCnameTarget = $config['brandedLinkOwnerCnameTarget'] ?? null;

        return $self;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'subdomain' => $this->subdomain,
            'valid' => $this->valid,
            'mailCnameHost' => $this->mailCnameHost,
            'mailCnameTarget' => $this->mailCnameTarget,
            'dkim1CnameHost' => $this->dkim1CnameHost,
            'dkim1CnameTarget' => $this->dkim1CnameTarget,
            'dkim2CnameHost' => $this->dkim2CnameHost,
            'dkim2CnameTarget' => $this->dkim2CnameTarget,
            'brandedLinkId' => $this->brandedLinkId,
            'brandedLinkValid' => $this->brandedLinkValid,
            'brandedLinkDomainCnameHost' => $this->brandedLinkDomainCnameHost,
            'brandedLinkDomainCnameTarget' => $this->brandedLinkDomainCnameTarget,
            'brandedLinkOwnerCnameHost' => $this->brandedLinkOwnerCnameHost,
            'brandedLinkOwnerCnameTarget' => $this->brandedLinkOwnerCnameTarget,
        ];
    }

    public function getCnameRecords(): array
    {
        return [
            ['host' => $this->mailCnameHost, 'target' => $this->mailCnameTarget],
            ['host' => $this->dkim1CnameHost, 'target' => $this->dkim1CnameTarget],
            ['host' => $this->dkim2CnameHost, 'target' => $this->dkim2CnameTarget],
            ['host' => $this->brandedLinkDomainCnameHost, 'target' => $this->brandedLinkDomainCnameTarget],
            ['host' => $this->brandedLinkOwnerCnameHost, 'target' => $this->brandedLinkOwnerCnameTarget],
        ];
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function updateDomainConfig(array $config)
    {
        $this->domain = $config['domain'] ?? null;
        $this->subdomain = $config['subdomain'] ?? null;
        $this->valid = $config['valid'] ?? false;
        $this->mailCnameHost = $config['dns']['mail_cname']['host'] ?? null;
        $this->mailCnameTarget = $config['dns']['mail_cname']['data'] ?? null;
        $this->dkim1CnameHost = $config['dns']['dkim1']['host'] ?? null;
        $this->dkim1CnameTarget = $config['dns']['dkim1']['data'] ?? null;
        $this->dkim2CnameHost = $config['dns']['dkim2']['host'] ?? null;
        $this->dkim2CnameTarget = $config['dns']['dkim2']['data'] ?? null;
    }

    public function setBrandedLinkId(int $brandedLinkId)
    {
        $this->brandedLinkId = $brandedLinkId;
    }

    public function updateBrandedLinkConfig(array $config)
    {
        $this->brandedLinkValid = $config['valid'] ?? false;
        $this->brandedLinkDomainCnameHost = $config['dns']['domain_cname']['host'] ?? null;
        $this->brandedLinkDomainCnameTarget = $config['dns']['domain_cname']['data'] ?? null;
        $this->brandedLinkOwnerCnameHost = $config['dns']['owner_cname']['host'] ?? null;
        $this->brandedLinkOwnerCnameTarget = $config['dns']['owner_cname']['data'] ?? null;
    }

    public function isReadyToProvision(): bool
    {
        return $this->isDomainReadyToProvision() && $this->isBrandedLinkReadyToProvision();
    }

    public function isDomainReadyToProvision(): bool
    {
        return $this->mailCnameHost
            && $this->dkim1CnameHost
            && $this->dkim2CnameHost;
    }

    public function isBrandedLinkReadyToProvision(): bool
    {
        return $this->brandedLinkDomainCnameHost
            && $this->brandedLinkOwnerCnameHost;
    }

    public function isFullyConfigured(): bool
    {
        return $this->valid && $this->brandedLinkValid;
    }

    public function isDomainAuthValid(): bool
    {
        return $this->valid;
    }

    public function isBrandedLinkValid(): bool
    {
        return $this->brandedLinkValid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function getMailCnameHost(): ?string
    {
        return $this->mailCnameHost;
    }

    public function getMailCnameTarget(): ?string
    {
        return $this->mailCnameTarget;
    }

    public function getDkim1CnameHost(): ?string
    {
        return $this->dkim1CnameHost;
    }

    public function getDkim1CnameTarget(): ?string
    {
        return $this->dkim1CnameTarget;
    }

    public function getDkim2CnameHost(): ?string
    {
        return $this->dkim2CnameHost;
    }

    public function getDkim2CnameTarget(): ?string
    {
        return $this->dkim2CnameTarget;
    }

    public function getBrandedLinkId(): ?int
    {
        return $this->brandedLinkId;
    }

    public function getBrandedLinkDomainCnameHost(): ?string
    {
        return $this->brandedLinkDomainCnameHost;
    }

    public function getBrandedLinkDomainCnameTarget(): ?string
    {
        return $this->brandedLinkDomainCnameTarget;
    }

    public function getBrandedLinkOwnerCnameHost(): ?string
    {
        return $this->brandedLinkOwnerCnameHost;
    }

    public function getBrandedLinkOwnerCnameTarget(): ?string
    {
        return $this->brandedLinkOwnerCnameTarget;
    }
}
