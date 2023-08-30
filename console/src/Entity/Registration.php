<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User registration that needs to be completed.
 */
#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
#[ORM\Table('registrations')]
#[UniqueEntity(fields: ['email'])]
class Registration
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 64, unique: true)]
    private string $token;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'invites')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Organization $organization;

    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin;

    #[ORM\Column(length: 6)]
    private string $locale;

    /**
     * Organization projects permissions for this member. Not used if
     * the member is admin of the organization.
     *
     * @see \App\Platform\Permissions for available permissions for each module.
     */
    #[ORM\Column(type: 'json')]
    private array $projectsPermissions;

    public function __construct(string $email, Organization $organization = null, bool $isAdmin = false, array $projectsPermissions = [], string $locale = 'en')
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->email = strtolower($email);
        $this->organization = $organization;
        $this->isAdmin = $isAdmin;
        $this->locale = $locale;
        $this->projectsPermissions = $isAdmin ? [] : $projectsPermissions;
        $this->token = bin2hex(random_bytes(32));
    }

    public static function createFixture(array $data): self
    {
        return new self($data['email'], $data['orga'] ?? null, $data['isAdmin'] ?? false, $data['permissions'] ?? []);
    }

    public function isTokenValid(string $token): bool
    {
        return $this->token === $token;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getProjectsPermissions(): array
    {
        return $this->projectsPermissions ?? [];
    }

    public function getConfiguredProjectsIds(): array
    {
        $keys = [];
        foreach ($this->projectsPermissions as $projectId => $permissions) {
            foreach ($permissions as $hasPermission) {
                if ($hasPermission) {
                    $keys[] = $projectId;
                    continue 2;
                }
            }
        }

        return $keys;
    }
}
