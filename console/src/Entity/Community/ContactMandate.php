<?php

namespace App\Entity\Community;

use App\Entity\Community\Enum\ContactMandateType;
use App\Entity\Util;
use App\Repository\Community\ContactMandateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactMandateRepository::class)]
#[ORM\Table(name: 'community_contacts_mandates')]
#[ORM\Index(columns: ['contact_id'], name: 'community_contacts_mandates_contact_idx')]
class ContactMandate
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'string', enumType: ContactMandateType::class)]
    private ContactMandateType $type;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $endAt;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = null;

    public function __construct(Contact $contact, ContactMandateType $type, string $label, \DateTimeImmutable $startAt, \DateTimeImmutable $endAt)
    {
        $this->populateTimestampable();
        $this->contact = $contact;
        $this->type = $type;
        $this->label = $label;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['contact'], $data['type'], $data['label'], $data['startAt'], $data['endAt']);
        $self->metadata = $data['metadata'] ?? null;

        return $self;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }
}
