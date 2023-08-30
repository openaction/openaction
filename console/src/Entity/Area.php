<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[Gedmo\Tree(type: 'nested')]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
#[ORM\Table('areas')]
#[ORM\Index(name: 'area_type_treeroot_name_idx', columns: ['type', 'tree_root', 'name'])]
#[ORM\Index(name: 'area_name_idx', columns: ['name'])]
#[ORM\Index(name: 'area_code_idx', columns: ['code'])]
#[ORM\Index(name: 'area_name_type_idx', columns: ['name', 'type'])]
class Area implements \Stringable
{
    public const TYPE_COUNTRY = 'country';
    public const TYPE_PROVINCE = 'province'; // US state, FR region, ...
    public const TYPE_DISTRICT = 'district'; // US county, FR department, ...
    public const TYPE_COMMUNITY = 'community'; // FR intercommunalité, ...
    public const TYPE_ZIP_CODE = 'zip_code';

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true], unique: true)]
    private int $id;

    #[ORM\Column(length: 10)]
    private string $type;

    #[ORM\Column(length: 40, unique: true)]
    private string $code;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $description;

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: 'Area', inversedBy: 'children')]
    private ?Area $parent;

    #[ORM\OneToMany(targetEntity: 'Area', mappedBy: 'parent')]
    private Collection $children;

    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: 'Area')]
    #[ORM\JoinColumn(name: 'tree_root', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Area $treeRoot;

    #[Gedmo\TreeLevel]
    #[ORM\Column(type: 'smallint')]
    private int $treeLevel;

    #[Gedmo\TreeLeft]
    #[ORM\Column(type: 'integer')]
    private int $treeLeft;

    #[Gedmo\TreeRight]
    #[ORM\Column(type: 'integer')]
    private int $treeRight;

    public function __construct(int $id, ?Area $parent, string $type, string $code, string $name, ?string $description = null)
    {
        $this->id = $id;
        $this->parent = $parent;
        $this->type = $type;
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
    }

    public static function createFixture(array $data): self
    {
        return new self(
            $data['id'],
            $data['parent'] ?? null,
            $data['type'],
            $data['code'],
            $data['name'] ?? $data['code'],
            $data['description'] ?? null
        );
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_COUNTRY,
            self::TYPE_PROVINCE,
            self::TYPE_DISTRICT,
            self::TYPE_COMMUNITY,
            self::TYPE_ZIP_CODE,
        ];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function contains(Area $child): bool
    {
        return $child->getTreeLeft() >= $this->getTreeLeft() && $child->getTreeRight() <= $this->getTreeRight();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParent(): ?Area
    {
        return $this->parent;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getFullPath(): array
    {
        if (!$this->parent) {
            return [$this];
        }

        return array_merge($this->parent->getFullPath(), [$this]);
    }

    public function getTreePath(): array
    {
        if (!$this->parent) {
            return [];
        }

        return array_merge($this->parent->getTreePath(), [$this->parent]);
    }

    public function getTreeRoot(): ?Area
    {
        return $this->treeRoot;
    }

    public function getTreeLevel(): int
    {
        return $this->treeLevel;
    }

    public function getTreeLeft(): int
    {
        return $this->treeLeft;
    }

    public function getTreeRight(): int
    {
        return $this->treeRight;
    }
}
