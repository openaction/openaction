<?php

namespace App\Entity\Community;

use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Community\ContactPaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactPaymentRepository::class)]
#[ORM\Table(name: 'community_contacts_payments')]
#[ORM\Index(columns: ['contact_id'], name: 'community_contacts_payments_contact_idx')]
#[ORM\Index(columns: ['type'], name: 'community_contacts_payments_type_idx')]
class ContactPayment
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'string', enumType: ContactPaymentType::class)]
    private ContactPaymentType $type;

    // Amounts in cents
    #[ORM\Column(type: 'bigint')]
    private int $netAmount;

    #[ORM\Column(type: 'bigint')]
    private int $feesAmount;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', enumType: ContactPaymentProvider::class)]
    private ContactPaymentProvider $paymentProvider;

    // Provider-specific details stored as a JSON document (PHP object serialized using dunglas/doctrine-json-odm)
    #[ORM\Column(type: 'json_document', nullable: true)]
    private ?object $paymentProviderDetails = null;

    #[ORM\Column(type: 'string', enumType: ContactPaymentMethod::class)]
    private ContactPaymentMethod $paymentMethod;

    // Lifecycle timestamps
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $capturedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $failedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $refundedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $canceledAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $receiptNumber = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $receipt = null;

    // Payer details snapshot
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

    // Membership specifics
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $membershipStartAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $membershipEndAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $membershipNumber = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = null;

    public function __construct(Contact $contact, ContactPaymentType $type, int $netAmount, int $feesAmount, string $currency, ContactPaymentProvider $provider, ContactPaymentMethod $method)
    {
        $this->populateTimestampable();
        $this->contact = $contact;
        $this->type = $type;
        $this->netAmount = $netAmount;
        $this->feesAmount = $feesAmount;
        $this->currency = strtoupper($currency);
        $this->paymentProvider = $provider;
        $this->paymentMethod = $method;
    }

    public static function createFixture(array $data): self
    {
        $self = new self(
            $data['contact'],
            $data['type'],
            $data['netAmount'],
            $data['feesAmount'],
            $data['currency'],
            $data['paymentProvider'],
            $data['paymentMethod'],
        );

        $self->paymentProviderDetails = $data['paymentProviderDetails'] ?? null;
        $self->capturedAt = $data['capturedAt'] ?? null;
        $self->failedAt = $data['failedAt'] ?? null;
        $self->refundedAt = $data['refundedAt'] ?? null;
        $self->canceledAt = $data['canceledAt'] ?? null;
        $self->receiptNumber = $data['receiptNumber'] ?? null;
        $self->receipt = $data['receipt'] ?? null;
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
        $self->membershipStartAt = $data['membershipStartAt'] ?? null;
        $self->membershipEndAt = $data['membershipEndAt'] ?? null;
        $self->membershipNumber = $data['membershipNumber'] ?? null;
        $self->metadata = $data['metadata'] ?? null;

        return $self;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function setPayerSnapshot(?string $civility, ?string $firstName, ?string $lastName, ?string $email, ?string $streetAddressLine1, ?string $streetAddressLine2, ?string $city, ?string $postalCode, ?string $countryCode, ?\DateTime $birthdate, ?string $phone, ?string $nationality, ?string $fiscalCountryCode): void
    {
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

    public function setMembershipPeriod(?\DateTimeImmutable $startAt, ?\DateTimeImmutable $endAt): void
    {
        $this->membershipStartAt = $startAt;
        $this->membershipEndAt = $endAt;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function setPaymentProviderDetails(?object $details): void
    {
        $this->paymentProviderDetails = $details;
    }

    public function getType(): ContactPaymentType
    {
        return $this->type;
    }

    public function getMembershipStartAt(): ?\DateTimeImmutable
    {
        return $this->membershipStartAt;
    }

    public function getMembershipEndAt(): ?\DateTimeImmutable
    {
        return $this->membershipEndAt;
    }
}
