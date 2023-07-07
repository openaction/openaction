<?php

namespace App\Entity\Community;

use App\Entity\Billing\Order;
use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Community\Printing\Model\PrintingOrderRecipientData;
use App\Form\Community\Printing\Model\PrintingOrderUnaddressedDeliveryData;
use App\Platform\Circonscriptions;
use App\Platform\Products;
use App\Repository\Community\PrintingOrderRepository;
use App\Search\Model\Searchable;
use App\Util\Address;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PrintingOrderRepository::class)]
#[ORM\Table('community_printing_orders')]
class PrintingOrder implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(type: 'json')]
    private array $status = ['draft' => 1];

    #[ORM\Column(type: 'boolean')]
    private bool $withEnveloping = false;

    #[ORM\Column(type: 'boolean')]
    private bool $deliveryAddressed = false;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $deliveryAddressFile = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $deliveryAddressFileFirstLines = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $deliveryAddressList = null;

    #[ORM\Column(type: 'boolean')]
    private bool $deliveryUseMediapost = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryMainAddressName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryMainAddressStreet1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryMainAddressStreet2 = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $deliveryMainAddressZipCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $deliveryMainAddressCity = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $deliveryMainAddressCountry = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $deliveryMainAddressInstructions = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $deliveryMainAddressProvider = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryMainAddressTrackingCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryPosterAddressName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryPosterAddressStreet1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryPosterAddressStreet2 = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $deliveryPosterAddressZipCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $deliveryPosterAddressCity = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $deliveryPosterAddressCountry = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $deliveryPosterAddressInstructions = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $deliveryPosterAddressProvider = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $deliveryPosterAddressTrackingCode = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $recipientDepartment = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $recipientCirconscription = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $recipientCandidate = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $recipientFirstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $recipientLastName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $recipientEmail = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $recipientPhone = null;

    #[ORM\OneToOne(targetEntity: Order::class, cascade: ['persist', 'remove'])]
    private ?Order $order = null;

    #[ORM\OneToMany(targetEntity: PrintingCampaign::class, mappedBy: 'printingOrder', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $campaigns;

    public function __construct(Project $project)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->campaigns = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new self($data['project']);
        $self->status = $data['status'] ?? $self->status;
        $self->withEnveloping = $data['withEnveloping'] ?? false;
        $self->deliveryAddressed = $data['deliveryAddressed'] ?? false;
        $self->deliveryAddressFile = $data['deliveryAddressFile'] ?? null;
        $self->deliveryAddressFileFirstLines = $data['deliveryAddressFileFirstLines'] ?? null;
        $self->deliveryAddressList = $data['deliveryAddressList'] ?? null;
        $self->deliveryUseMediapost = $data['deliveryUseMediapost'] ?? false;
        $self->deliveryMainAddressName = $data['deliveryMainAddressName'] ?? null;
        $self->deliveryMainAddressStreet1 = $data['deliveryMainAddressStreet1'] ?? null;
        $self->deliveryMainAddressStreet2 = $data['deliveryMainAddressStreet2'] ?? null;
        $self->deliveryMainAddressZipCode = $data['deliveryMainAddressZipCode'] ?? null;
        $self->deliveryMainAddressCity = Address::formatCityName($data['deliveryMainAddressCity'] ?? null);
        $self->deliveryMainAddressCountry = $data['deliveryMainAddressCountry'] ?? null;
        $self->deliveryMainAddressInstructions = $data['deliveryMainAddressInstructions'] ?? null;
        $self->deliveryPosterAddressName = $data['deliveryPosterAddressName'] ?? null;
        $self->deliveryPosterAddressStreet1 = $data['deliveryPosterAddressStreet1'] ?? null;
        $self->deliveryPosterAddressStreet2 = $data['deliveryPosterAddressStreet2'] ?? null;
        $self->deliveryPosterAddressZipCode = $data['deliveryPosterAddressZipCode'] ?? null;
        $self->deliveryPosterAddressCity = Address::formatCityName($data['deliveryPosterAddressCity'] ?? null);
        $self->deliveryPosterAddressCountry = $data['deliveryPosterAddressCountry'] ?? null;
        $self->deliveryPosterAddressInstructions = $data['deliveryPosterAddressInstructions'] ?? null;
        $self->recipientCandidate = $data['recipientCandidate'] ?? null;
        $self->recipientDepartment = $data['recipientDepartment'] ?? null;
        $self->recipientCirconscription = $data['recipientCirconscription'] ?? null;
        $self->recipientFirstName = $data['recipientFirstName'] ?? null;
        $self->recipientLastName = $data['recipientLastName'] ?? null;
        $self->recipientEmail = $data['recipientEmail'] ?? null;
        $self->recipientPhone = $data['recipientPhone'] ?? null;
        $self->order = $data['order'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    /*
     * Setters
     */
    public function applyAddressedDelivery(Upload $addessList, array $firstLines)
    {
        $this->withEnveloping = true;
        $this->deliveryAddressed = true;
        $this->deliveryAddressFile = $addessList;
        $this->deliveryAddressFileFirstLines = $firstLines;
        $this->deliveryAddressList = null;
        $this->deliveryUseMediapost = false;
        $this->deliveryMainAddressStreet1 = null;
        $this->deliveryMainAddressStreet2 = null;
        $this->deliveryMainAddressZipCode = null;
        $this->deliveryMainAddressCity = null;
        $this->deliveryMainAddressCountry = null;
        $this->deliveryMainAddressInstructions = null;
    }

    public function applyAddressedDeliveryParsedList(array $list)
    {
        // Addressed
        $this->deliveryAddressList = $list;
        $this->deliveryAddressFile = null;
        $this->deliveryAddressFileFirstLines = null;

        // Quantities
        foreach ($this->getCampaigns() as $campaign) {
            $campaign->setQuantity(count($list));
        }
    }

    public function applyUnaddressedDelivery(PrintingOrderUnaddressedDeliveryData $data)
    {
        // Unaddressed
        $this->deliveryAddressed = false;
        $this->deliveryAddressList = null;
        $this->deliveryAddressFileFirstLines = null;

        // Eneveloping
        $this->deliveryUseMediapost = $data->useMediapost;
        if (!$data->useMediapost) {
            // Never envelop when not using Mediapost
            $this->withEnveloping = false;
        } else {
            // Give choice when using Mediapost
            $this->withEnveloping = $data->withEnveloping;
        }

        // Delivery addresses
        $this->deliveryMainAddressName = $data->addressName;
        $this->deliveryMainAddressStreet1 = $data->addressStreet1;
        $this->deliveryMainAddressStreet2 = $data->addressStreet2;
        $this->deliveryMainAddressZipCode = $data->addressZipCode;
        $this->deliveryMainAddressCity = Address::formatCityName($data->addressCity);
        $this->deliveryMainAddressCountry = $data->addressCountry;
        $this->deliveryMainAddressInstructions = $data->addressInstructions;
        $this->deliveryPosterAddressName = $data->posterAddressName;
        $this->deliveryPosterAddressStreet1 = $data->posterAddressStreet1;
        $this->deliveryPosterAddressStreet2 = $data->posterAddressStreet2;
        $this->deliveryPosterAddressZipCode = $data->posterAddressZipCode;
        $this->deliveryPosterAddressCity = Address::formatCityName($data->posterAddressCity);
        $this->deliveryPosterAddressCountry = $data->posterAddressCountry;
        $this->deliveryPosterAddressInstructions = $data->posterAddressInstructions;

        // Quantities
        foreach ($this->getCampaigns() as $campaign) {
            $campaign->setQuantity($data->quantities[$campaign->getId()] ?? 500);
        }
    }

    public function updateMainDeliveryStatus(string $provider, string $trackingCode = null)
    {
        $this->deliveryMainAddressProvider = $provider;
        $this->deliveryMainAddressTrackingCode = $trackingCode;
    }

    public function updatePosterDeliveryStatus(string $provider, string $trackingCode = null)
    {
        $this->deliveryPosterAddressProvider = $provider;
        $this->deliveryPosterAddressTrackingCode = $trackingCode;
    }

    public function applyRecipientData(PrintingOrderRecipientData $data)
    {
        $circonscriptionParts = explode('-', $data->circonscription);
        $this->recipientDepartment = $circonscriptionParts[0];
        $this->recipientCirconscription = $circonscriptionParts[1];
        $this->recipientCandidate = $data->candidate;
        $this->recipientFirstName = $data->firstName;
        $this->recipientLastName = $data->lastName;
        $this->recipientEmail = $data->email;
        $this->recipientPhone = $data->phone;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /*
     * Accessors
     */
    public function isWithEnveloping(): bool
    {
        return $this->withEnveloping;
    }

    public function isReadyToOrder(): bool
    {
        return
            $this->isDeliveryReadyToOrder()
            && $this->isRecipientReadyToOrder()
            && $this->isContentReadyToOrder()
        ;
    }

    public function isContentReadyToOrder(): bool
    {
        if (!$this->getCampaigns()->count()) {
            return false;
        }

        foreach ($this->getCampaigns() as $campaign) {
            if (!$campaign->isReadyToOrder()) {
                return false;
            }
        }

        return true;
    }

    public function isDeliveryReadyToOrder(): bool
    {
        if ($this->deliveryUseMediapost) {
            return true;
        }

        if ($this->deliveryAddressed) {
            return $this->deliveryAddressList && count($this->deliveryAddressList) > 0;
        }

        // Only posters/banners: check only poster address
        if ([Products::PRINT_OFFICIAL_BANNER, Products::PRINT_OFFICIAL_POSTER] === $this->getProductsCodes()
            || [Products::PRINT_OFFICIAL_BANNER] === $this->getProductsCodes()
            || [Products::PRINT_OFFICIAL_POSTER] === $this->getProductsCodes()) {
            return $this->deliveryPosterAddressStreet1
                && $this->deliveryPosterAddressZipCode
                && $this->deliveryPosterAddressCity
                && $this->deliveryPosterAddressCountry
            ;
        }

        // No posters/banners: check only main address
        if (!in_array(Products::PRINT_OFFICIAL_BANNER, $this->getProductsCodes(), true)
            && !in_array(Products::PRINT_OFFICIAL_POSTER, $this->getProductsCodes(), true)) {
            return $this->deliveryMainAddressStreet1
                && $this->deliveryMainAddressZipCode
                && $this->deliveryMainAddressCity
                && $this->deliveryMainAddressCountry
            ;
        }

        // Both types: check both addresses
        return $this->deliveryMainAddressStreet1
            && $this->deliveryMainAddressZipCode
            && $this->deliveryMainAddressCity
            && $this->deliveryMainAddressCountry
            && $this->deliveryPosterAddressStreet1
            && $this->deliveryPosterAddressZipCode
            && $this->deliveryPosterAddressCity
            && $this->deliveryPosterAddressCountry
        ;
    }

    public function isRecipientReadyToOrder(): bool
    {
        return $this->recipientDepartment
            && $this->recipientCirconscription
            && $this->recipientCandidate
            && $this->recipientFirstName
            && $this->recipientLastName
            && $this->recipientEmail
            && $this->recipientPhone
        ;
    }

    public function isWaitingForAction(): bool
    {
        if (!$this->order) {
            return false;
        }

        if (!$this->order->getPaidAt()) {
            return true;
        }

        foreach ($this->getCampaigns() as $campaign) {
            if ($campaign->getBat() && !$campaign->getBatValidatedAt()) {
                return true;
            }
        }

        return false;
    }

    public function hasCampaigns(): bool
    {
        return $this->getCampaigns()->count() > 0;
    }

    public function getProductsCodes(): array
    {
        $codes = [];
        foreach ($this->getCampaigns() as $campaign) {
            $codes[] = $campaign->getProduct();
        }

        sort($codes);

        return $codes;
    }

    public function allBatValidated(): bool
    {
        foreach ($this->getCampaigns() as $campaign) {
            if (!$campaign->getBatValidatedAt()) {
                return false;
            }
        }

        return true;
    }

    public function isOfficialOrder(): bool
    {
        foreach ($this->getCampaigns() as $campaign) {
            if (str_starts_with($campaign->getProduct(), 'official_')) {
                return true;
            }
        }

        return false;
    }

    public function isSubrogatedOrder(): bool
    {
        return $this->isOfficialOrder() && $this->project->getOrganization()->isPrintSubrogated();
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
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'printing-order';
    }

    public function isSearchPublic(): bool
    {
        return false;
    }

    public function getSearchUuid(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function getSearchTitle(): string
    {
        return Uid::toBase62($this->uuid);
    }

    public function getSearchContent(): ?string
    {
        return null;
    }

    public function getSearchCategoriesFacet(): array
    {
        return [];
    }

    public function getSearchStatusFacet(): ?string
    {
        if ($this->getOrder()) {
            return 'ordered';
        }

        return 'draft';
    }

    public function getSearchAreaTreeFacet(): array
    {
        return [];
    }

    public function getSearchDateFacet(): ?int
    {
        return (int) $this->createdAt->format('U');
    }

    public function getSearchMetadata(): array
    {
        return [
            'project' => $this->project->getUuid()->toRfc4122(),
            'projectName' => $this->project->getName(),
            'orderedAt' => $this->order?->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /*
     * Getters
     */
    public function getReference(): string
    {
        return strtoupper(substr($this->uuid->toRfc4122(), 0, 8));
    }

    public function isDeliveryAddressed(): bool
    {
        return $this->deliveryAddressed;
    }

    public function getDeliveryAddressFile(): ?Upload
    {
        return $this->deliveryAddressFile;
    }

    public function getDeliveryAddressFileFirstLines(): ?array
    {
        return $this->deliveryAddressFileFirstLines;
    }

    public function getDeliveryAddressList(): ?array
    {
        return $this->deliveryAddressList;
    }

    public function isDeliveryUseMediapost(): bool
    {
        return $this->deliveryUseMediapost;
    }

    public function getDeliveryMainAddressName(): ?string
    {
        return $this->deliveryMainAddressName;
    }

    public function getDeliveryMainAddressStreet1(): ?string
    {
        return $this->deliveryMainAddressStreet1;
    }

    public function getDeliveryMainAddressStreet2(): ?string
    {
        return $this->deliveryMainAddressStreet2;
    }

    public function getDeliveryMainAddressZipCode(): ?string
    {
        return $this->deliveryMainAddressZipCode;
    }

    public function getDeliveryMainAddressCity(): ?string
    {
        return $this->deliveryMainAddressCity;
    }

    public function getDeliveryMainAddressCountry(): ?string
    {
        return $this->deliveryMainAddressCountry;
    }

    public function getDeliveryMainAddressInstructions(): ?string
    {
        return $this->deliveryMainAddressInstructions;
    }

    public function getDeliveryMainAddressProvider(): ?string
    {
        return $this->deliveryMainAddressProvider;
    }

    public function getDeliveryMainAddressTrackingCode(): ?string
    {
        return $this->deliveryMainAddressTrackingCode;
    }

    public function getDeliveryPosterAddressName(): ?string
    {
        return $this->deliveryPosterAddressName;
    }

    public function getDeliveryPosterAddressStreet1(): ?string
    {
        return $this->deliveryPosterAddressStreet1;
    }

    public function getDeliveryPosterAddressStreet2(): ?string
    {
        return $this->deliveryPosterAddressStreet2;
    }

    public function getDeliveryPosterAddressZipCode(): ?string
    {
        return $this->deliveryPosterAddressZipCode;
    }

    public function getDeliveryPosterAddressCity(): ?string
    {
        return $this->deliveryPosterAddressCity;
    }

    public function getDeliveryPosterAddressCountry(): ?string
    {
        return $this->deliveryPosterAddressCountry;
    }

    public function getDeliveryPosterAddressInstructions(): ?string
    {
        return $this->deliveryPosterAddressInstructions;
    }

    public function getDeliveryPosterAddressProvider(): ?string
    {
        return $this->deliveryPosterAddressProvider;
    }

    public function getDeliveryPosterAddressTrackingCode(): ?string
    {
        return $this->deliveryPosterAddressTrackingCode;
    }

    public function getRecipientDepartment(): ?string
    {
        return $this->recipientDepartment;
    }

    public function getRecipientDepartmentName(): ?string
    {
        return $this->recipientDepartment ? Circonscriptions::getFrDepartmentName($this->recipientDepartment) : null;
    }

    public function getRecipientCandidate(): ?string
    {
        return $this->recipientCandidate;
    }

    public function getRecipientCirconscription(): ?string
    {
        return $this->recipientCirconscription;
    }

    public function getRecipientFirstName(): ?string
    {
        return $this->recipientFirstName;
    }

    public function getRecipientLastName(): ?string
    {
        return $this->recipientLastName;
    }

    public function getRecipientEmail(): ?string
    {
        return $this->recipientEmail;
    }

    public function getRecipientPhone(): ?string
    {
        return $this->recipientPhone;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @return Collection|PrintingCampaign[]
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }
}
