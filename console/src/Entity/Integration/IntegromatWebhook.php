<?php

namespace App\Entity\Integration;

use App\Entity\Organization;
use App\Entity\Util;
use App\Repository\Integration\IntegromatWebhookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntegromatWebhookRepository::class)]
#[ORM\Table('integrations_integromat_webhooks')]
class IntegromatWebhook
{
    use Util\EntityIdTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 64, unique: true)]
    private string $token;

    #[ORM\Column(length: 64, unique: true)]
    private string $integromatUrl;

    public function __construct(Organization $organization, string $url)
    {
        $this->populateTimestampable();
        $this->token = bin2hex(random_bytes(32));
        $this->organization = $organization;
        $this->integromatUrl = $url;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['orga'], $data['url']);
        $self->token = $data['token'] ?? $self->token;

        return $self;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getIntegromatUrl(): string
    {
        return $this->integromatUrl;
    }
}
