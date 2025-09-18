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

    #[ORM\OneToOne(targetEntity: Form::class, inversedBy: 'localizedPetition')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Form $form;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $image = null;

    #[ORM\Column(length: 10)]
    private string $locale;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $submitButtonLabel;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $optinLabel;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $legalities;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $addressedTo = null;

    /**
     * @var Collection<PetitionCategory>
     */
    #[ORM\ManyToMany(targetEntity: PetitionCategory::class, inversedBy: 'petitions')]
    #[ORM\JoinTable(name: 'website_petitions_localized_petitions_localized_categories')]
    private Collection $categories;

    public function __construct(Petition $petition, ?Form $form, string $locale, string $title, string $submitButtonLabel, string $optinLabel, string $legalities)
    {
        $this->populateTimestampable();
        $this->petition = $petition;
        $this->uuid = Uid::random();
        $this->categories = new ArrayCollection();
        $this->form = $form;
        $this->locale = $locale;
        $this->title = $title;
        $this->submitButtonLabel = $submitButtonLabel;
        $this->optinLabel = $optinLabel;
        $this->legalities = $legalities;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['petition'], $data['form'] ?? null, $data['locale'], $data['title'], '', '', '');
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

    public function setImage(?Upload $image): void
    {
        $this->image = $image;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getLocaleFlag(): string
    {
        return 'en' === $this->locale ? 'gb' : $this->locale;
    }

    public function getPetition(): Petition
    {
        return $this->petition;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getSubmitButtonLabel(): ?string
    {
        return $this->submitButtonLabel;
    }

    public function getOptinLabel(): ?string
    {
        return $this->optinLabel;
    }

    public function getAddressedTo(): ?string
    {
        return $this->addressedTo;
    }

    public function applyContentUpdate(\App\Form\Website\Model\LocalizedPetitionData $data): void
    {
        $this->title = (string) ($data->title ?? '');
        $this->content = (string) ($data->content ?? '');
    }

    public function applyMetadataUpdate(\App\Form\Website\Model\LocalizedPetitionData $data): void
    {
        $this->description = (string) ($data->description ?? '');
        $this->submitButtonLabel = $data->submitButtonLabel ?: null;
        $this->optinLabel = $data->optinLabel ?: null;
        $this->addressedTo = $data->addressedTo ?: null;
    }

    /**
     * @return Collection<PetitionCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }
}
