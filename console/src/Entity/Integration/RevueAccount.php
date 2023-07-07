<?php

namespace App\Entity\Integration;

use App\Entity\Organization;
use App\Entity\Util;
use App\Form\Integration\Model\RevueAccountData;
use App\Repository\Integration\RevueAccountRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RevueAccountRepository::class)]
#[ORM\Table(name: 'integrations_revue_accounts')]
class RevueAccount
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 50)]
    private string $label;

    #[ORM\Column(length: 80)]
    private string $apiToken;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lastSync = null;

    public function __construct(Organization $organization, string $label, string $apiToken)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->label = $label;
        $this->apiToken = $apiToken;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['orga'], $data['label'], $data['apiToken']);
        $self->enabled = $data['enabled'] ?? true;
        $self->lastSync = $data['lastSync'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function applyDataUpdate(RevueAccountData $data)
    {
        $this->label = $data->label ?: '';
        $this->apiToken = $data->apiToken ?: '';
    }

    public function markSynced()
    {
        $this->enabled = true;
        $this->lastSync = new \DateTime();
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getLastSync(): ?\DateTime
    {
        return $this->lastSync;
    }
}
