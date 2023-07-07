<?php

namespace App\Form\Community\Printing\Model;

use App\Entity\Community\PrintingOrder;
use Symfony\Component\Validator\Constraints as Assert;

class PrintingOrderUnaddressedDeliveryData
{
    public bool $withEnveloping = false;
    public bool $useMediapost = false;

    #[Assert\Length(max: 100)]
    public ?string $addressName = null;

    #[Assert\Length(max: 100)]
    public ?string $addressStreet1 = null;

    #[Assert\Length(max: 100)]
    public ?string $addressStreet2 = null;

    #[Assert\Length(max: 10)]
    public ?string $addressZipCode = null;

    #[Assert\Length(max: 50)]
    public ?string $addressCity = null;

    #[Assert\Length(max: 2)]
    #[Assert\Country]
    public ?string $addressCountry = null;

    public ?string $addressInstructions = null;

    #[Assert\Length(max: 100)]
    public ?string $posterAddressName = null;

    #[Assert\Length(max: 100)]
    public ?string $posterAddressStreet1 = null;

    #[Assert\Length(max: 100)]
    public ?string $posterAddressStreet2 = null;

    #[Assert\Length(max: 10)]
    public ?string $posterAddressZipCode = null;

    #[Assert\Length(max: 50)]
    public ?string $posterAddressCity = null;

    #[Assert\Length(max: 2)]
    #[Assert\Country]
    public ?string $posterAddressCountry = null;

    public ?string $posterAddressInstructions = null;

    public array $quantities = [];

    public static function fromCampaign(PrintingOrder $order): self
    {
        $self = new self();
        $self->withEnveloping = $order->isWithEnveloping();
        $self->useMediapost = $order->isDeliveryUseMediapost();
        $self->addressName = $order->getDeliveryMainAddressName();
        $self->addressStreet1 = $order->getDeliveryMainAddressStreet1();
        $self->addressStreet2 = $order->getDeliveryMainAddressStreet2();
        $self->addressZipCode = $order->getDeliveryMainAddressZipCode();
        $self->addressCity = $order->getDeliveryMainAddressCity();
        $self->addressCountry = $order->getDeliveryMainAddressCountry();
        $self->addressInstructions = $order->getDeliveryMainAddressInstructions();
        $self->posterAddressName = $order->getDeliveryPosterAddressName();
        $self->posterAddressStreet1 = $order->getDeliveryPosterAddressStreet1();
        $self->posterAddressStreet2 = $order->getDeliveryPosterAddressStreet2();
        $self->posterAddressZipCode = $order->getDeliveryPosterAddressZipCode();
        $self->posterAddressCity = $order->getDeliveryPosterAddressCity();
        $self->posterAddressCountry = $order->getDeliveryPosterAddressCountry();
        $self->posterAddressInstructions = $order->getDeliveryPosterAddressInstructions();

        foreach ($order->getCampaigns() as $campaign) {
            $self->quantities[$campaign->getId()] = $campaign->getQuantity();
        }

        return $self;
    }
}
