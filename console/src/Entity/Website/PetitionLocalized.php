<?php

namespace App\Entity\Website;

use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\PetitionLocalizedData;
use App\Repository\Website\PetitionLocalizedRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PetitionLocalizedRepository::class)]
#[ORM\Table(name: 'website_petitions_localized')]
class PetitionLocalized
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    public const LOCALE_FR = 'fr';
    public const LOCALE_EN = 'gb';
    public const LOCALE_DE = 'de';
    public const LOCALE_IT = 'it';
    public const LOCALE_NL = 'nl';
    public const LOCALE_PT = 'pt';

    #[ORM\Column(length: 10)]
    private ?string $locale;

    #[ORM\Column(length: 200)]
    private ?string $title;

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

    #[ORM\ManyToOne(targetEntity: Petition::class, inversedBy: 'localized')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Petition $petition;

    #[ORM\ManyToMany(targetEntity: PetitionCategory::class, inversedBy: 'petitionsLocalized')]
    #[ORM\JoinTable(name: 'website_petitions_localized_petitions_localized_categories')]
    private Collection $categories;

    #[ORM\OneToOne(targetEntity: Form::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'SET NULL')]
    private Form $form;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $image = null;

    public function __construct(Petition $petition, string $title, string $formTitle, string $locale)
    {
        $this->populateTimestampable();
        $this->petition = $petition;
        $this->uuid = Uid::random();
        $this->title = $title;
        $this->locale = $locale;
        $this->form = new Form($petition->getProject(), $formTitle);
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getSubmitButtonLabel(): ?string
    {
        return $this->submitButtonLabel;
    }

    public function setSubmitButtonLabel(?string $submitButtonLabel): void
    {
        $this->submitButtonLabel = $submitButtonLabel;
    }

    public function getOptinLabel(): ?string
    {
        return $this->optinLabel;
    }

    public function setOptinLabel(?string $optinLabel): void
    {
        $this->optinLabel = $optinLabel;
    }

    public function getLegalities(): ?string
    {
        return $this->legalities;
    }

    public function setLegalities(?string $legalities): void
    {
        $this->legalities = $legalities;
    }

    public function getAddressedTo(): ?string
    {
        return $this->addressedTo;
    }

    public function setAddressedTo(?string $addressedTo): void
    {
        $this->addressedTo = $addressedTo;
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

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }

    public function setImage(?Upload $image): void
    {
        $this->image = $image;
    }

    public function applyContentUpdate(PetitionLocalizedData $data): void
    {
        $this->title = (string) $data->title;
        $this->content = (string) $data->content;
    }

    public function applyMetadataUpdate(PetitionLocalizedData $data): void
    {
        $this->description = (string) $data->description;
        $this->addressedTo = (string) $data->addressedTo;
        $this->submitButtonLabel = (string) $data->submitButtonLabel;
        $this->optinLabel = (string) $data->optinLabel;
        $this->legalities = (string) $data->legalities;
    }
}
