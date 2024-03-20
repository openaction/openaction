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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PetitionCategoryRepository::class)]
#[ORM\Table(name: 'website_petitions_localized_categories')]
class PetitionCategory
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(type: 'string', length: 40)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $slug;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    #[ORM\ManyToMany(targetEntity: PetitionLocalized::class, mappedBy: 'categories')]
    #[ORM\JoinTable(name: 'website_petitions_localized_petitions_localized_categories')]
    private Collection $petitionsLocalized;

    public function __construct(Project $project, string $name, int $weight = 1)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->setName($name);
        $this->weight = $weight;
        $this->petitionsLocalized = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = (string) $name;
        $this->slug = (new AsciiSlugger())->slug($this->name)->lower();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getPetitionsLocalized(): Collection
    {
        return $this->petitionsLocalized;
    }
}
