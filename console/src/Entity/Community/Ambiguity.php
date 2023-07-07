<?php

namespace App\Entity\Community;

use App\Entity\Organization;
use App\Entity\Util;
use App\Repository\Community\AmbiguityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AmbiguityRepository::class)]
#[ORM\Table('community_ambiguities')]
#[ORM\UniqueConstraint(name: 'community_ambiguity_match', columns: ['oldest_id', 'newest_id'])]
class Ambiguity
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $oldest;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $newest;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $ignoredAt = null;

    public function __construct(Organization $organization, Contact $oldest, Contact $newest)
    {
        $this->organization = $organization;
        $this->oldest = $oldest;
        $this->newest = $newest;
    }

    public static function createFixture(array $data): self
    {
        return new self($data['orga'], $data['oldest'], $data['newest']);
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function getOldest(): ?Contact
    {
        return $this->oldest;
    }

    public function getNewest(): ?Contact
    {
        return $this->newest;
    }

    public function getIgnoredAt(): ?\DateTimeInterface
    {
        return $this->ignoredAt;
    }

    public function updateIgnoredAt(?\DateTime $ignoredAt): void
    {
        $this->ignoredAt = $ignoredAt;
    }
}
