<?php

namespace App\Entity;

use App\Entity\Community\Tag;
use App\Repository\OrganizationMainTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationMainTagRepository::class)]
#[ORM\Table('organizations_main_tags')]
class OrganizationMainTag
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'mainTags')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: Tag::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tag $tag;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    public function __construct(Organization $organization, Tag $tag, int $weight)
    {
        $this->organization = $organization;
        $this->tag = $tag;
        $this->weight = $weight;
    }

    public function __toString()
    {
        return (string) $this->tag;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
