<?php

namespace App\Entity\Website;

use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Website\PetitionLocalizedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PetitionLocalizedRepository::class)]
#[ORM\Table(name: 'website_petitions_localized')]
class PetitionLocalized
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 10)]
    private ?string $locale = null;

    #[ORM\Column(length: 200)]
    private ?string $title = null;

    #[ORM\Column(length: 200)]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(length: 30)]
    private string $submitButtonLabel;

    #[ORM\Column(length: 30)]
    private string $optinLabel;

    #[ORM\ManyToOne(targetEntity: Petition::class, inversedBy: 'localized')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Petition $petition;

    #[ORM\ManyToMany(targetEntity: PetitionCategory::class, inversedBy: 'petitionsLocalized')]
    #[ORM\JoinTable(name: 'website_petitions_localized_petitions_localized_categories')]
    private Collection $categories;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $mainImage = null;

    public function __construct(Petition $petition)
    {
        $this->populateTimestampable();
        $this->petition = $petition;
        $this->categories = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getSubmitButtonLabel(): string
    {
        return $this->submitButtonLabel;
    }

    public function setSubmitButtonLabel(string $submitButtonLabel): void
    {
        $this->submitButtonLabel = $submitButtonLabel;
    }

    public function getOptinLabel(): string
    {
        return $this->optinLabel;
    }

    public function setOptinLabel(string $optinLabel): void
    {
        $this->optinLabel = $optinLabel;
    }

    public function getPetition(): Petition
    {
        return $this->petition;
    }

    /** @return Collection<PetitionCategory> */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getMainImage(): ?Upload
    {
        return $this->mainImage;
    }

    public function setMainImage(?Upload $mainImage): void
    {
        $this->mainImage = $mainImage;
    }
}
