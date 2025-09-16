<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Repository\Website\PetitionCategoryRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PetitionCategoryRepository::class)]
#[ORM\Table('website_petitions_localized_categories')]
class PetitionCategory
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;

    #[ORM\Column(length: 40)]
    private string $name;

    #[ORM\Column(length: 50)]
    private string $slug;

    #[ORM\Column(type: 'integer')]
    private int $weight = 1;

    /**
     * @var Collection<LocalizedPetition>
     */
    #[ORM\ManyToMany(targetEntity: LocalizedPetition::class, mappedBy: 'categories')]
    private Collection $petitions;

    public function __construct(Project $project, string $name, int $weight = 1)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->name = $name;
        $this->slug = (new AsciiSlugger())->slug($this->name)->lower();
        $this->weight = $weight;
        $this->petitions = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['name'], $data['weight'] ?? 1);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['name']);
        $self->slug = $data['slug'] ?? $self->slug;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
