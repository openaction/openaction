<?php

namespace App\Entity\Billing;

use App\Billing\Model\OrderLine;
use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Organization;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Billing\QuoteRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
#[ORM\Table(name: 'billing_quotes')]
class Quote
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

    /**
     * The billing company code ({@see \App\Platform\Company}).
     */
    #[ORM\Column(length: 30)]
    private string $company;

    /**
     * The details of the quote recipient.
     */
    #[ORM\Column(type: 'json')]
    private array $recipient;

    /**
     * The details of the quote.
     */
    #[ORM\Column(type: 'json')]
    private array $lines;

    /**
     * The amount in € cents (used as a description, the important value is in the Mollie quote).
     */
    #[ORM\Column(type: 'bigint')]
    private int $amount;

    /**
     * The quote number.
     */
    #[ORM\Column(type: 'bigint', unique: true, nullable: true)]
    private int $number;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $pdf = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt = null;

    /**
     * @param OrderLine[] $lines
     */
    public function __construct(string $company, Organization $orga, OrderRecipient $recipient, array $lines, int $amount, int $number)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->company = $company;
        $this->organization = $orga;
        $this->recipient = $recipient->toArray();
        $this->amount = $amount;
        $this->number = $number;

        $this->lines = [];
        foreach ($lines as $line) {
            $this->lines[] = $line->toArray();
        }
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['company'], $data['orga'], $data['recipient'], $data['lines'] ?? [], $data['amount'], $data['number'] ?? 1);
        $self->uuid = (isset($data['uuid']) && $data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::random();
        $self->pdf = $data['pdf'] ?? null;
        $self->sentAt = $data['sentAt'] ?? null;

        return $self;
    }

    public function setPdf(?Upload $pdf)
    {
        $this->pdf = $pdf;
    }

    public function markPdfSent()
    {
        $this->sentAt = new \DateTime();
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getRecipient(): OrderRecipient
    {
        return OrderRecipient::fromArray($this->recipient);
    }

    public function getRecipientDescription(): string
    {
        return (string) $this->getRecipient();
    }

    public function getAmountDescription(): string
    {
        return number_format($this->amount / 100, 2, ',', ' ').' €';
    }

    /**
     * @return OrderLine[]
     */
    public function getLines(): array
    {
        $lines = [];
        foreach ($this->lines as $l) {
            $lines[] = OrderLine::fromArray($l);
        }

        return $lines;
    }

    public function getRawLines(): array
    {
        return $this->lines;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getPdf(): ?Upload
    {
        return $this->pdf;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }
}
