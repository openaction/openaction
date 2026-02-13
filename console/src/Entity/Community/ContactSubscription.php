<?php

namespace App\Entity\Community;

use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Entity\Util;
use App\Repository\Community\ContactSubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactSubscriptionRepository::class)]
#[ORM\Table(name: 'community_contacts_subscriptions')]
#[ORM\Index(columns: ['contact_id'], name: 'community_contacts_subscriptions_contact_idx')]
#[ORM\Index(columns: ['active', 'ends_at'], name: 'community_contacts_subscriptions_active_idx')]
class ContactSubscription
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'string', enumType: ContactPaymentType::class)]
    private ContactPaymentType $type;

    #[ORM\Column(type: 'bigint')]
    private int $netAmount;

    #[ORM\Column(type: 'bigint')]
    private int $feesAmount;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', enumType: ContactPaymentMethod::class)]
    private ContactPaymentMethod $paymentMethod;

    #[ORM\Column(type: 'integer')]
    private int $intervalInMonths;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $civility = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $streetAddressLine1 = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $streetAddressLine2 = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $birthdate = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $nationality = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $fiscalCountryCode = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = null;

    public function __construct(
        Contact $contact,
        ContactPaymentType $type,
        int $netAmount,
        int $feesAmount,
        string $currency,
        ContactPaymentMethod $paymentMethod,
        int $intervalInMonths,
        \DateTimeImmutable $startsAt,
        ?\DateTimeImmutable $endsAt,
    ) {
        $this->populateTimestampable();
        $this->contact = $contact;
        $this->type = $type;
        $this->netAmount = $netAmount;
        $this->feesAmount = $feesAmount;
        $this->currency = strtoupper($currency);
        $this->paymentMethod = $paymentMethod;
        $this->intervalInMonths = $intervalInMonths;
        $this->startsAt = $startsAt;
        $this->endsAt = $endsAt;
    }

    public static function createFixture(array $data): self
    {
        $self = new self(
            $data['contact'],
            $data['type'],
            $data['netAmount'],
            $data['feesAmount'],
            $data['currency'],
            $data['paymentMethod'],
            $data['intervalInMonths'],
            $data['startsAt'],
            $data['endsAt'] ?? null,
        );

        $self->active = $data['active'] ?? true;
        $self->civility = $data['civility'] ?? null;
        $self->firstName = $data['firstName'] ?? null;
        $self->lastName = $data['lastName'] ?? null;
        $self->email = $data['email'] ?? null;
        $self->streetAddressLine1 = $data['streetAddressLine1'] ?? null;
        $self->streetAddressLine2 = $data['streetAddressLine2'] ?? null;
        $self->city = $data['city'] ?? null;
        $self->postalCode = $data['postalCode'] ?? null;
        $self->countryCode = $data['countryCode'] ?? null;
        $self->birthdate = $data['birthdate'] ?? null;
        $self->phone = $data['phone'] ?? null;
        $self->nationality = $data['nationality'] ?? null;
        $self->fiscalCountryCode = $data['fiscalCountryCode'] ?? null;
        $self->metadata = $data['metadata'] ?? null;

        return $self;
    }

    public function updateSchedule(
        int $netAmount,
        int $feesAmount,
        string $currency,
        ContactPaymentMethod $paymentMethod,
        int $intervalInMonths,
        \DateTimeImmutable $startsAt,
        ?\DateTimeImmutable $endsAt,
    ): void {
        $this->netAmount = $netAmount;
        $this->feesAmount = $feesAmount;
        $this->currency = strtoupper($currency);
        $this->paymentMethod = $paymentMethod;
        $this->intervalInMonths = $intervalInMonths;
        $this->startsAt = $startsAt;
        $this->endsAt = $endsAt;
        $this->active = true;
    }

    public function setPayerSnapshot(
        ?string $civility,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $streetAddressLine1,
        ?string $streetAddressLine2,
        ?string $city,
        ?string $postalCode,
        ?string $countryCode,
        ?\DateTime $birthdate,
        ?string $phone,
        ?string $nationality,
        ?string $fiscalCountryCode,
    ): void {
        $this->civility = $civility;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->streetAddressLine1 = $streetAddressLine1;
        $this->streetAddressLine2 = $streetAddressLine2;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->countryCode = $countryCode ? strtoupper($countryCode) : null;
        $this->birthdate = $birthdate;
        $this->phone = $phone;
        $this->nationality = $nationality ? strtoupper($nationality) : null;
        $this->fiscalCountryCode = $fiscalCountryCode ? strtoupper($fiscalCountryCode) : null;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getType(): ContactPaymentType
    {
        return $this->type;
    }

    public function getIntervalInMonths(): int
    {
        return $this->intervalInMonths;
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function createPaymentForDate(\DateTimeImmutable $date): ContactPayment
    {
        $payment = new ContactPayment(
            $this->contact,
            $this->type,
            $this->netAmount,
            $this->feesAmount,
            $this->currency,
            ContactPaymentProvider::Manual,
            $this->paymentMethod,
        );

        $payment->setCreatedAt(\DateTime::createFromImmutable($date));
        $payment->setSubscription($this);
        $payment->setPayerSnapshot(
            $this->civility,
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->streetAddressLine1,
            $this->streetAddressLine2,
            $this->city,
            $this->postalCode,
            $this->countryCode,
            $this->birthdate,
            $this->phone,
            $this->nationality,
            $this->fiscalCountryCode,
        );
        $payment->setMetadata($this->metadata ?: null);

        if (ContactPaymentType::Membership === $this->type) {
            $membershipEnd = $date->modify(sprintf('+%d months -1 day', $this->intervalInMonths));
            $payment->setMembershipPeriod($date, $membershipEnd);
        }

        return $payment;
    }
}
