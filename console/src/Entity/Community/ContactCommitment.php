<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\ContactCommitmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactCommitmentRepository::class)]
#[ORM\Table(name: 'community_contacts_commitments')]
#[ORM\Index(columns: ['contact_id'], name: 'community_contacts_commitments_contact_idx')]
class ContactCommitment
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function __construct(Contact $contact)
    {
        $this->populateTimestampable();
        $this->contact = $contact;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['contact']);
        $self->label = $data['label'];
        $self->startAt = $data['startAt'] ?? null;
        $self->metadata = $data['metadata'] ?? null;

        return $self;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }
}
