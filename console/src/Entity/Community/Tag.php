<?php

namespace App\Entity\Community;

use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Util;
use App\Repository\Community\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table('community_tags', uniqueConstraints: [
    new ORM\UniqueConstraint('community_tags_name_organization_idx', ['name', 'organization_id']),
])]
#[ORM\Index(columns: ['slug'], name: 'community_tags_slug_idx')]
class Tag
{
    use Util\EntityIdTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $name;

    #[ORM\Column(length: 150)]
    private string $slug;

    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'metadataTags', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'community_contacts_tags')]
    private Collection $contacts;

    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'tags', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'projects_tags')]
    private Collection $projects;

    public function __construct(Organization $organization, string $name, ?string $slug = null)
    {
        $this->populateTimestampable();
        $this->organization = $organization;
        $this->name = u($name)->slice(0, 150);
        $this->slug = (string) ($slug ?: (new AsciiSlugger())->slug($this->name)->lower());
        $this->contacts = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        return new self($data['orga'], $data['name'], $data['slug'] ?? null);
    }

    public function duplicate(Organization $organization): self
    {
        return new self($organization, $this->name, $this->slug);
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = u($name)->slice(0, 150);

        if ($this->name) {
            $this->slug = (string) (new AsciiSlugger())->slug($this->name)->lower();
        }
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }
}
