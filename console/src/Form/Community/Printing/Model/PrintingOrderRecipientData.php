<?php

namespace App\Form\Community\Printing\Model;

use App\Entity\Community\PrintingOrder;
use Symfony\Component\Validator\Constraints as Assert;

class PrintingOrderRecipientData
{
    #[Assert\NotBlank]
    public ?string $circonscription = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $candidate = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 100)]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $phone = null;

    public static function fromOrder(PrintingOrder $order): self
    {
        $self = new self();
        $self->circonscription = $order->getRecipientDepartment().'-'.$order->getRecipientCirconscription();
        $self->candidate = $order->getRecipientCandidate();
        $self->firstName = $order->getRecipientFirstName();
        $self->lastName = $order->getRecipientLastName();
        $self->email = $order->getRecipientEmail();
        $self->phone = $order->getRecipientPhone();

        return $self;
    }
}
