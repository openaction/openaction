<?php

namespace App\Entity\Integration;

use App\Entity\Organization;
use App\Entity\User;
use App\Entity\Util;
use App\Repository\Integration\TelegramAppAuthorizationRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TelegramAppAuthorizationRepository::class)]
#[ORM\Table(name: 'integrations_telegram_apps_authorizations')]
class TelegramAppAuthorization implements UserInterface
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: TelegramApp::class, inversedBy: 'authorizations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TelegramApp $app;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'telegramAppsAuthorizations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $member;

    #[ORM\Column(length: 80)]
    private string $apiToken;

    public function __construct(TelegramApp $app, User $member)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->app = $app;
        $this->member = $member;
        $this->apiToken = 'telegram_'.bin2hex(random_bytes(32));
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['app'], $data['member']);

        if (isset($data['apiToken']) && $data['apiToken']) {
            $self->apiToken = $data['apiToken'];
        }

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function getApp(): TelegramApp
    {
        return $this->app;
    }

    public function getOrganization(): Organization
    {
        return $this->app->getOrganization();
    }

    public function getMember(): User
    {
        return $this->member;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    /*
     * UserInterface
     */
    public function getRoles(): array
    {
        return $this->member->getRoles();
    }

    public function getPassword(): string
    {
        return $this->apiToken;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername(): string
    {
        return $this->member->getUsername();
    }

    public function getUserIdentifier(): string
    {
        return $this->member->getUserIdentifier();
    }
}
