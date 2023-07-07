<?php

namespace App\Entity\Billing;

use App\Billing\Model\OrderLine;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Organization;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Billing\OrderRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'billing_orders')]
class Order
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
     * The Mollie order ID.
     */
    #[ORM\Column(length: 100, unique: true, nullable: true)]
    private ?string $mollieId;

    /**
     * The details of the order recipient.
     */
    #[ORM\Column(type: 'json')]
    private array $recipient;

    /**
     * The details of the action to apply when the order is paid.
     */
    #[ORM\Column(type: 'json')]
    private array $action;

    /**
     * The details of the order.
     */
    #[ORM\Column(type: 'json')]
    private array $lines;

    /**
     * The amount in € cents (used as a description, the important value is in the Mollie order).
     */
    #[ORM\Column(type: 'bigint')]
    private int $amount;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $paidAt = null;

    /**
     * The invoice number.
     */
    #[ORM\Column(type: 'bigint', unique: true, nullable: true)]
    private ?int $invoiceNumber = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $invoicePdf = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $invoiceSentAt = null;

    public function __construct(Uuid $uuid, string $company, Organization $orga, ?string $mollieId, OrderAction $action, OrderRecipient $recipient, array $lines, int $amount)
    {
        $this->populateTimestampable();
        $this->uuid = $uuid;
        $this->company = $company;
        $this->organization = $orga;
        $this->mollieId = $mollieId;
        $this->recipient = $recipient->toArray();
        $this->action = $action->toArray();
        $this->amount = $amount;

        $this->lines = [];
        foreach ($lines as $line) {
            $this->lines[] = $line->toArray();
        }
    }

    public static function createFixture(array $data): self
    {
        $uuid = (isset($data['uuid']) && $data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::random();

        $self = new self($uuid, $data['company'], $data['orga'], $data['mollieId'], $data['action'], $data['recipient'], $data['lines'] ?? [], $data['amount']);
        $self->paidAt = $data['paidAt'] ?? null;
        $self->invoiceNumber = $data['invoiceNumber'] ?? null;
        $self->invoicePdf = $data['invoicePdf'] ?? null;
        $self->invoiceSentAt = $data['invoiceSentAt'] ?? null;

        return $self;
    }

    /*
     * Setters
     */
    public function markToPay(int $invoiceNumber)
    {
        if (!$this->invoiceNumber) {
            $this->invoiceNumber = $invoiceNumber;
        }
    }

    public function markPaid(int $invoiceNumber, \DateTime $paidAt)
    {
        if (!$this->invoiceNumber) {
            $this->invoiceNumber = $invoiceNumber;
        }

        $this->paidAt = $paidAt;
    }

    public function setInvoicePdf(Upload $invoicePdf)
    {
        $this->invoicePdf = $invoicePdf;
    }

    public function markInvoiceSent()
    {
        $this->invoiceSentAt = new \DateTime();
    }

    /*
     * Getters
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    public function getMollieId(): ?string
    {
        return $this->mollieId;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
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

    public function getAction(): OrderAction
    {
        return OrderAction::fromArray($this->action);
    }

    public function getRecipient(): OrderRecipient
    {
        return OrderRecipient::fromArray($this->recipient);
    }

    public function getInvoiceNumber(): ?int
    {
        return $this->invoiceNumber;
    }

    public function getInvoicePdf(): ?Upload
    {
        return $this->invoicePdf;
    }

    public function getInvoiceSentAt(): ?\DateTime
    {
        return $this->invoiceSentAt;
    }

    public function hasInvoice(): bool
    {
        return null !== $this->invoicePdf;
    }

    public function getPaidAt(): ?\DateTime
    {
        return $this->paidAt;
    }
}
