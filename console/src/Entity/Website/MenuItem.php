<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Form\Appearance\Model\WebsiteMenuItemData;
use App\Repository\Website\MenuItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
#[ORM\Table('website_menu_items')]
class MenuItem implements \Stringable
{
    use Util\EntityIdTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    public const POSITION_HEADER = 'header';
    public const POSITION_FOOTER = 'footer';

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?MenuItem $parent;

    #[ORM\Column(length: 20)]
    private string $position;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    private string $label;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $url;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    #[ORM\Column(type: 'integer')]
    private bool $openNewTab = false;

    /**
     * @var Collection|MenuItem[]
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private Collection $children;

    public function __construct(Project $project, string $position, string $label, string $url, int $weight = 1, self $parent = null)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->parent = $parent;
        $this->position = $position;
        $this->label = $label;
        $this->url = $url;
        $this->weight = $weight;
        $this->children = new ArrayCollection();
    }

    public function duplicate(self $parent = null): self
    {
        $self = new self($this->project, $this->position, $this->label, $this->url, $this->weight);
        $self->openNewTab = $this->openNewTab;
        $self->parent = $parent;

        return $self;
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['position'], $data['label'], $data['url']);
        $self->parent = $data['parent'] ?? null;
        $self->weight = $data['weight'] ?? 1;
        $self->openNewTab = $data['openNewTab'] ?? false;

        return $self;
    }

    public function applyDataUpdate(WebsiteMenuItemData $data)
    {
        $this->parent = $data->parent;
        $this->label = (string) $data->label;
        $this->url = (string) $data->url;
        $this->openNewTab = (bool) $data->openNewTab;
    }

    public static function getPositions(): array
    {
        return [
            self::POSITION_HEADER,
            self::POSITION_FOOTER,
        ];
    }

    public function getParent(): ?MenuItem
    {
        return $this->parent;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function isOpenNewTab(): bool
    {
        return $this->openNewTab;
    }

    /**
     * @return MenuItem[]|Collection
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }
}
