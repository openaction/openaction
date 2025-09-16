<?php

namespace App\Entity\Website;

use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Website\LocalizedPetitionRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LocalizedPetitionRepository::class)]
#[ORM\Table('website_petitions_localized')]
class LocalizedPetition
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Petition::class, inversedBy: 'localizations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Petition $petition;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Form $form = null;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $image = null;

    #[ORM\Column(length: 10)]
    private string $locale;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $submitButtonLabel = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $optinLabel = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $legalities = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $addressedTo = null;

    /**
     * @var Collection<PetitionCategory>
     */
    #[ORM\ManyToMany(targetEntity: PetitionCategory::class, inversedBy: 'petitions')]
    #[ORM\JoinTable(name: 'website_petitions_localized_petitions_localized_categories')]
    private Collection $categories;

    public function __construct(Petition $petition, string $locale, string $title)
    {
        $this->populateTimestampable();
        $this->petition = $petition;
        $this->uuid = Uid::random();
        $this->locale = $locale;
        $this->title = $title;
        $this->categories = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['petition'], $data['locale'], $data['title']);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['title']);
        $self->description = $data['description'] ?? null;
        $self->content = $data['content'] ?? null;
        $self->submitButtonLabel = $data['submitButtonLabel'] ?? null;
        $self->optinLabel = $data['optinLabel'] ?? null;
        $self->legalities = $data['legalities'] ?? null;
        $self->addressedTo = $data['addressedTo'] ?? null;
        $self->image = $data['image'] ?? null;
        $self->form = $data['form'] ?? null;
        foreach ($data['categories'] ?? [] as $category) {
            $self->categories[] = $category;
        }

        return $self;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }

    /**
     * @return Collection<PetitionCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }
}
