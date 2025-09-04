<?php

namespace App\Entity;

use App\Entity\Community\Contact;
use App\Entity\Util;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('contact_memberships')]
#[ORM\Index(columns: ['organization_id'], name: 'contact_memberships_organization_idx')]
#[ORM\Index(columns: ['project_id'], name: 'contact_memberships_project_idx')]
#[ORM\Index(columns: ['contact_id'], name: 'contact_memberships_contact_idx')]
#[ORM\Index(columns: ['status'], name: 'contact_memberships_status_idx')]
class ContactMembership
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\ManyToOne(targetEntity: MembershipPlan::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?MembershipPlan $plan = null;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $amount;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 10)]
    private string $interval; // monthly, yearly, one_off

    #[ORM\Column(length: 20)]
    private string $status = 'pending'; // pending, active, past_due, canceled, expired

    #[ORM\Column(length: 10)]
    private string $origin = 'mollie'; // mollie, manual, import

    #[ORM\Column(type: 'boolean')]
    private bool $autoRenew = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $currentPeriodStart = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $currentPeriodEnd = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $mollieSubscriptionId = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $mollieCustomerId = null;

    public function __construct(Organization $organization, Project $project, Contact $contact, int $amount, string $currency, string $interval)
    {
        $this->populateTimestampable();
        $this->organization = $organization;
        $this->project = $project;
        $this->contact = $contact;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->interval = $interval;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getPlan(): ?MembershipPlan
    {
        return $this->plan;
    }

    public function setPlan(?MembershipPlan $plan): void
    {
        $this->plan = $plan;
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

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function setInterval(string $interval): void
    {
        $this->interval = $interval;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function isAutoRenew(): bool
    {
        return $this->autoRenew;
    }

    public function setAutoRenew(bool $autoRenew): void
    {
        $this->autoRenew = $autoRenew;
    }

    public function getCurrentPeriodStart(): ?\DateTime
    {
        return $this->currentPeriodStart;
    }

    public function setCurrentPeriodStart(?\DateTime $date): void
    {
        $this->currentPeriodStart = $date;
    }

    public function getCurrentPeriodEnd(): ?\DateTime
    {
        return $this->currentPeriodEnd;
    }

    public function setCurrentPeriodEnd(?\DateTime $date): void
    {
        $this->currentPeriodEnd = $date;
    }

    public function getMollieSubscriptionId(): ?string
    {
        return $this->mollieSubscriptionId;
    }

    public function setMollieSubscriptionId(?string $id): void
    {
        $this->mollieSubscriptionId = $id ?: null;
    }

    public function getMollieCustomerId(): ?string
    {
        return $this->mollieCustomerId;
    }

    public function setMollieCustomerId(?string $id): void
    {
        $this->mollieCustomerId = $id ?: null;
    }
}

