<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Repository\Website\PetitionRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

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
    private string $slug;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $startAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $endAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $signaturesGoal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $signaturesCount = null;

    /**
     * @var Collection<TrombinoscopePerson>
     */
    #[ORM\ManyToMany(targetEntity: TrombinoscopePerson::class, inversedBy: 'petitions')]
    #[ORM\JoinTable(name: 'website_petitions_authors')]
    #[ORM\OrderBy(['publishedAt' => 'DESC'])]
    private Collection $authors;

    /**
     * @var Collection<LocalizedPetition>
     */
    #[ORM\OneToMany(mappedBy: 'petition', targetEntity: LocalizedPetition::class, cascade: ['persist', 'remove'])]
    private Collection $localizations;

    public function __construct(Project $project, string $slug)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->slug = (new AsciiSlugger())->slug($slug)->lower();
        $this->authors = new ArrayCollection();
        $this->localizations = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['slug']);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['slug']);
        $self->publishedAt = $data['publishedAt'] ?? null;
        $self->startAt = $data['startAt'] ?? null;
        $self->endAt = $data['endAt'] ?? null;
        $self->signaturesGoal = $data['signaturesGoal'] ?? null;
        $self->signaturesCount = $data['signaturesCount'] ?? null;
        $self->externalUrl = $data['externalUrl'] ?? null;
        $self->pageViews = $data['pageViews'] ?? 0;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;

        foreach ($data['authors'] ?? [] as $author) {
            $self->authors[] = $author;
        }

        return $self;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setPublishedAt(?\DateTime $date = null): void
    {
        $this->publishedAt = $date;
    }
}
