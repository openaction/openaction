<?php

namespace App\Entity;

use App\Entity\Community\Contact;
use App\Entity\Util;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('donations')]
#[ORM\Index(columns: ['organization_id'], name: 'donations_organization_idx')]
#[ORM\Index(columns: ['project_id'], name: 'donations_project_idx')]
#[ORM\Index(columns: ['contact_id'], name: 'donations_contact_idx')]
#[ORM\Index(columns: ['status'], name: 'donations_status_idx')]
class Donation
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Project $project = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $amount;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(type: 'boolean')]
    private bool $isRecurring = false;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $frequency = null; // one_off, monthly, yearly

    #[ORM\Column(length: 20)]
    private string $status = 'pending';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $method = null;

    #[ORM\Column(length: 100, unique: true, nullable: true)]
    private ?string $molliePaymentId = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $mollieSubscriptionId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column(length: 10)]
    private string $origin = 'mollie'; // mollie, manual, import

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $paidAt = null;

    public function __construct(Organization $organization, Contact $contact, int $amount, string $currency)
    {
        $this->populateTimestampable();
        $this->organization = $organization;
        $this->contact = $contact;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = strtoupper($currency);
    }

    public function isRecurring(): bool
    {
        return $this->isRecurring;
    }

    public function setRecurring(bool $recurring, ?string $frequency = null): void
    {
        $this->isRecurring = $recurring;
        $this->frequency = $frequency;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): void
    {
        $this->method = $method ?: null;
    }

    public function getMolliePaymentId(): ?string
    {
        return $this->molliePaymentId;
    }

    public function setMolliePaymentId(?string $id): void
    {
        $this->molliePaymentId = $id ?: null;
    }

    public function getMollieSubscriptionId(): ?string
    {
        return $this->mollieSubscriptionId;
    }

    public function setMollieSubscriptionId(?string $id): void
    {
        $this->mollieSubscriptionId = $id ?: null;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description ?: null;
    }

    public function getMetadata(): array
    {
        return $this->metadata ?: [];
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata ?: null;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function getPaidAt(): ?\DateTime
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }
}

