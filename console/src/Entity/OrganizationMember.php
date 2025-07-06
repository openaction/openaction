<?php

namespace App\Entity;

use App\Entity\Model\ProjectsPermissions;
use App\Form\Organization\Model\MemberPermissionData;
use App\Repository\OrganizationMemberRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrganizationMemberRepository::class)]
#[ORM\Table('organizations_members')]
class OrganizationMember
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $member;

    /**
     * Whether the member is administrator of the organization
     * (ie. can configure the orga and has full access to all projects).
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin;

    /**
     * Custom labels associated to members and exposed in API to help building custom tools.
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $labels = null;

    /**
     * Organization projects permissions for this member. Not used if
     * the member is admin of the organization.
     *
     * @see \App\Platform\Permissions for available permissions for each module.
     */
    #[ORM\Column(type: 'json')]
    private array $projectsPermissions;

    /**
     * Organization projects permissions categories for this member. Not used if
     * the member is admin of the organization.
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $projectsPermissionsCategories = null;

    /**
     * Tenant token to use for search in the organization community. This token contains not only security credentials,
     * but also instructions on which documents within that index the member is allowed to see.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $crmTenantToken = null;

    public function __construct(Organization $orga, User $member, bool $isAdmin = false, array $projectsPermissions = [])
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->organization = $orga;
        $this->member = $member;
        $this->isAdmin = $isAdmin;
        $this->projectsPermissions = $isAdmin ? [] : $projectsPermissions;
    }

    public static function createFromRegistration(User $user, Registration $invite): self
    {
        return new self($invite->getOrganization(), $user, $invite->isAdmin(), $invite->getProjectsPermissions());
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['orga'], $data['user'], $data['isAdmin'] ?? true, $data['permissions'] ?? []);
        $self->labels = $data['labels'] ?? [];

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function applyPermissionsUpdate(MemberPermissionData $data)
    {
        $this->setPermissions($data->isAdmin, $data->isAdmin ? [] : $data->parseProjectPermissionsArray());
        $this->setProjectsPermissionsCategories($data->isAdmin ? [] : $data->parseProjectPermissionsCategoriesArray());
        $this->labels = $data->parseLabelsArray();
    }

    public function setPermissions(bool $isAdmin, array $projectsPermissions)
    {
        $this->isAdmin = $isAdmin;
        $this->projectsPermissions = $projectsPermissions;
    }

    public function setProjectsPermissionsCategories(?array $projectsPermissionsCategories)
    {
        $this->projectsPermissionsCategories = $projectsPermissionsCategories;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getMember(): User
    {
        return $this->member;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function getLabels(): array
    {
        return $this->labels ?: [];
    }

    public function getRawProjectsPermissions(): array
    {
        return $this->projectsPermissions;
    }

    public function getRawProjectsPermissionsCategories(): ?array
    {
        return $this->projectsPermissionsCategories;
    }

    public function getProjectsPermissions(): ProjectsPermissions
    {
        return new ProjectsPermissions($this->isAdmin, $this->projectsPermissions);
    }

    public function getCrmTenantToken(): ?string
    {
        return $this->crmTenantToken;
    }

    public function setCrmTenantToken(?string $crmTenantToken)
    {
        $this->crmTenantToken = $crmTenantToken;
    }
}
