<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Repository\Website\PetitionRepository;
use App\Util\Uid;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PetitionRepository::class)]
#[ORM\Table('website_petitions')]
class Petition
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;
    use Util\EntityMemberRestrictedTrait;
    use Util\EntityPageViewsTrait;
    use Util\EntityExternalUrlTrait;

    #[ORM\Column(length: 200)]
    private ?string $slug = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $publishedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $startAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $endAt = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ['unsigned' => true])]
    private ?int $signaturesGoal = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ['unsigned' => true])]
    private ?int $signaturesCount = null;

    #[ORM\OneToMany(mappedBy: 'petition', targetEntity: PetitionLocalized::class, cascade: ['persist', 'remove'])]
    private Collection $localized;

    /** @var Collection<TrombinoscopePerson> */
    #[ORM\ManyToMany(targetEntity: TrombinoscopePerson::class, inversedBy: 'petitions')]
    #[ORM\JoinTable(name: 'website_petitions_authors')]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private Collection $authors;

    public function __construct(Project $project)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->authors = new ArrayCollection();
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function isPublished(): bool
    {
        return $this->publishedAt && $this->publishedAt < new \DateTime();
    }

    public function isDraft(): bool
    {
        return !$this->publishedAt;
    }

    public function getStartAt(): ?DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTime $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): ?DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTime $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getSignaturesGoal(): ?int
    {
        return $this->signaturesGoal;
    }

    public function setSignaturesGoal(?int $signaturesGoal): void
    {
        $this->signaturesGoal = $signaturesGoal;
    }

    public function getSignaturesCount(): ?int
    {
        return $this->signaturesCount;
    }

    public function setSignaturesCount(?int $signaturesCount): void
    {
        $this->signaturesCount = $signaturesCount;
    }

    /** @return Collection<PetitionLocalized> */
    public function getLocalized(): Collection
    {
        return $this->localized;
    }

    /** @return Collection<TrombinoscopePerson> */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }
}
