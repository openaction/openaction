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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

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
        $self->label = $data['label'] ?? null;
        $self->startAt = $data['startAt'] ?? null;
        $self->metadata = $data['metadata'] ?? null;

        return $self;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }
}
