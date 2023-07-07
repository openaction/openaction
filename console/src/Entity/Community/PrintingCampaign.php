<?php

namespace App\Entity\Community;

use App\Entity\Community\Model\PrintingCampaignProductionStatus;
use App\Entity\Community\Model\PrintingCampaignSourceError;
use App\Entity\Upload;
use App\Entity\Util;
use App\Platform\PrintFiles;
use App\Repository\Community\PrintingCampaignRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PrintingCampaignRepository::class)]
#[ORM\Table('community_printing_campaigns')]
class PrintingCampaign
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(type: 'json')]
    private array $status = ['draft' => 1];

    #[ORM\ManyToOne(targetEntity: PrintingOrder::class, inversedBy: 'campaigns', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PrintingOrder $printingOrder;

    #[ORM\Column(type: 'json')]
    private array $productionStatus;

    #[ORM\Column(length: 40)]
    private string $product;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quantity;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $source = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $sourceError = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $preview = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $bat = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $batErrors = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $batWarnings = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $batValidatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $printedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deliveredAt = null;

    public function __construct(PrintingOrder $order, string $product)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->printingOrder = $order;
        $this->product = $product;
        $this->quantity = PrintFiles::QUANTITIES_BY_PRODUCT[$product][0];
        $this->productionStatus = (new PrintingCampaignProductionStatus())->toArray();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new self($data['printingOrder'], $data['product']);
        $self->source = $data['source'] ?? null;
        $self->preview = $data['preview'] ?? null;
        $self->status = $data['status'] ?? $self->status;
        $self->quantity = $data['quantity'] ?? $self->quantity;
        $self->productionStatus = ($data['productionStatus'] ?? new PrintingCampaignProductionStatus())->toArray();
        $self->bat = $data['bat'] ?? null;
        $self->batValidatedAt = $data['batValidatedAt'] ?? null;
        $self->printedAt = $data['printedAt'] ?? null;
        $self->deliveredAt = $data['deliveredAt'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    /*
     * Workflow
     */
    public function setStatus(array $status)
    {
        $this->status = $status;
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    /*
     * Setters
     */
    public function updateSourceFile(?Upload $source, ?Upload $preview)
    {
        $this->source = $source;
        $this->preview = $preview;
    }

    public function setSourceError(?PrintingCampaignSourceError $sourceError)
    {
        $this->sourceError = $sourceError?->toArray();
    }

    public function setProduct(string $product)
    {
        $this->product = $product;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function receiveBat(?Upload $bat, array $errors = [], array $warnings = [])
    {
        $this->bat = $bat;
        $this->batErrors = $errors ?: null;
        $this->batWarnings = $warnings ?: null;
    }

    public function validateBat()
    {
        $this->batValidatedAt = new \DateTime();
    }

    /*
     * Accessors
     */
    public function isReadyToOrder(): bool
    {
        return null !== $this->source;
    }

    /*
     * Getters
     */
    public function getReference(): string
    {
        return Uid::toBase62($this->uuid);
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getPrintingOrder(): PrintingOrder
    {
        return $this->printingOrder;
    }

    public function getProductionStatus(): PrintingCampaignProductionStatus
    {
        return PrintingCampaignProductionStatus::fromArray($this->productionStatus);
    }

    public function getStatusDescription(): string
    {
        return implode(', ', array_keys($this->status));
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function getSource(): ?Upload
    {
        return $this->source;
    }

    public function getSourceError(): ?PrintingCampaignSourceError
    {
        return $this->sourceError ? PrintingCampaignSourceError::fromArray($this->sourceError) : null;
    }

    public function getPreview(): ?Upload
    {
        return $this->preview;
    }

    public function getBat(): ?Upload
    {
        return $this->bat;
    }

    public function getBatErrors(): ?array
    {
        return $this->batErrors;
    }

    public function getBatWarnings(): ?array
    {
        return $this->batWarnings;
    }

    public function getBatValidatedAt(): ?\DateTime
    {
        return $this->batValidatedAt;
    }

    public function getPrintedAt(): ?\DateTime
    {
        return $this->printedAt;
    }

    public function getDeliveredAt(): ?\DateTime
    {
        return $this->deliveredAt;
    }
}
