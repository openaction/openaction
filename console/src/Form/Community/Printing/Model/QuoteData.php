<?php

namespace App\Form\Community\Printing\Model;

use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Entity\Organization;
use App\Entity\Project;
use App\Form\Billing\Model\UpdateBillingDetailsData;
use App\Platform\PrintFiles;
use Symfony\Component\Validator\Constraints as Assert;

class QuoteData
{
    public array $quantities = [];

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $deliveryStreet1 = null;

    #[Assert\Length(max: 100)]
    public ?string $deliveryStreet2 = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    public ?string $deliveryZipCode = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $deliveryCity = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 2)]
    #[Assert\Country]
    public ?string $deliveryCountry = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $billingOrganization = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 100)]
    public ?string $billingEmail = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $billingStreet1 = null;

    #[Assert\Length(max: 200)]
    public ?string $billingStreet2 = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 25)]
    public ?string $billingZipCode = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $billingCity = null;

    #[Assert\NotBlank]
    #[Assert\Country]
    public ?string $billingCountry = null;

    public function __construct(Organization $orga)
    {
        $this->billingOrganization = $orga->getBillingName();
        $this->billingEmail = $orga->getBillingEmail();
        $this->billingStreet1 = $orga->getBillingAddressStreetLine1();
        $this->billingStreet2 = $orga->getBillingAddressStreetLine2();
        $this->billingZipCode = $orga->getBillingAddressPostalCode();
        $this->billingCity = $orga->getBillingAddressCity();
        $this->billingCountry = $orga->getBillingAddressCountry();
        $this->deliveryStreet1 = $orga->getBillingAddressStreetLine1();
        $this->deliveryStreet2 = $orga->getBillingAddressStreetLine2();
        $this->deliveryZipCode = $orga->getBillingAddressPostalCode();
        $this->deliveryCity = $orga->getBillingAddressCity();
        $this->deliveryCountry = $orga->getBillingAddressCountry();
    }

    public function createUpdateBillingDetailsData(Organization $orga): UpdateBillingDetailsData
    {
        $data = new UpdateBillingDetailsData();
        $data->name = $this->billingOrganization;
        $data->email = $this->billingEmail;
        $data->streetLine1 = $this->billingStreet1;
        $data->streetLine2 = $this->billingStreet2;
        $data->postalCode = $this->billingZipCode;
        $data->city = $this->billingCity;
        $data->country = $this->billingCountry;

        return $data;
    }

    public function createPrintingOrder(Project $project): PrintingOrder
    {
        $order = new PrintingOrder($project);

        // Delivery
        $delivery = new PrintingOrderUnaddressedDeliveryData();
        $delivery->addressStreet1 = $this->deliveryStreet1;
        $delivery->addressStreet2 = $this->deliveryStreet2;
        $delivery->addressZipCode = $this->deliveryZipCode;
        $delivery->addressCity = $this->deliveryCity;
        $delivery->addressCountry = $this->deliveryCountry;
        $order->applyUnaddressedDelivery($delivery);

        // Populate campaigns
        foreach ($this->quantities as $product => $quantity) {
            if (0 === $quantity) {
                continue;
            }

            while ($quantity > 0) {
                $campaignQuantity = $this->resolveLargestQuantityIncludedIn($product, $quantity);

                $campaign = new PrintingCampaign($order, $product);
                $campaign->setQuantity($campaignQuantity);

                $order->getCampaigns()->add($campaign);

                $quantity -= $campaignQuantity;
            }
        }

        return $order;
    }

    private function resolveLargestQuantityIncludedIn(string $product, int $quantity): int
    {
        foreach (PrintFiles::QUANTITIES_BY_PRODUCT[$product] as $allowedQuantity) {
            if ($quantity <= $allowedQuantity) {
                return $allowedQuantity;
            }
        }

        return array_reverse(PrintFiles::QUANTITIES_BY_PRODUCT[$product])[0];
    }
}
