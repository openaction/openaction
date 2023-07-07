<?php

namespace App\Entity\Integration;

use App\Entity\Organization;
use App\Entity\Util;
use App\Repository\Integration\TelegramAppRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TelegramAppRepository::class)]
#[ORM\Table(name: 'integrations_telegram_apps')]
class TelegramApp
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 50)]
    private string $botUsername;

    /**
     * @var TelegramAppAuthorization[]|Collection
     */
    #[ORM\OneToMany(targetEntity: TelegramAppAuthorization::class, mappedBy: 'app', orphanRemoval: true)]
    private Collection $authorizations;

    public function __construct(Organization $organization, string $botUsername)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->botUsername = $botUsername;
        $this->authorizations = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['orga'], $data['username']);
        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function getBotUsername(): string
    {
        return $this->botUsername;
    }

    public function getAuthorizations(): ArrayCollection|Collection|array
    {
        return $this->authorizations;
    }
}
