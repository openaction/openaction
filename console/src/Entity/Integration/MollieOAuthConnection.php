<?php

namespace App\Entity\Integration;

use App\Entity\Organization;
use App\Entity\Util;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('integration_mollie_oauth_connections')]
class MollieOAuthConnection
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\OneToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $clientId = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $scopes = null;

    #[ORM\Column(type: 'text')]
    private string $refreshToken;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $accessToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $accessTokenExpiresAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mollieOrganizationId = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $mollieOrganizationName = null;

    #[ORM\Column(type: 'boolean')]
    private bool $testmode = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $capabilities = null;

    public function __construct(Organization $organization, string $encryptedRefreshToken)
    {
        $this->populateTimestampable();
        $this->organization = $organization;
        $this->refreshToken = $encryptedRefreshToken;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId ?: null;
    }

    public function getScopes(): array
    {
        return $this->scopes ?: [];
    }

    public function setScopes(?array $scopes): void
    {
        $this->scopes = $scopes ?: null;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $encryptedRefreshToken): void
    {
        $this->refreshToken = $encryptedRefreshToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken, ?\DateTime $expiresAt): void
    {
        $this->accessToken = $accessToken ?: null;
        $this->accessTokenExpiresAt = $expiresAt;
    }

    public function getAccessTokenExpiresAt(): ?\DateTime
    {
        return $this->accessTokenExpiresAt;
    }

    public function getMollieOrganizationId(): ?string
    {
        return $this->mollieOrganizationId;
    }

    public function setMollieOrganizationId(?string $id): void
    {
        $this->mollieOrganizationId = $id ?: null;
    }

    public function getMollieOrganizationName(): ?string
    {
        return $this->mollieOrganizationName;
    }

    public function setMollieOrganizationName(?string $name): void
    {
        $this->mollieOrganizationName = $name ?: null;
    }

    public function isTestmode(): bool
    {
        return $this->testmode;
    }

    public function setTestmode(bool $testmode): void
    {
        $this->testmode = $testmode;
    }

    public function getCapabilities(): array
    {
        return $this->capabilities ?: [];
    }

    public function setCapabilities(?array $capabilities): void
    {
        $this->capabilities = $capabilities ?: null;
    }
}

