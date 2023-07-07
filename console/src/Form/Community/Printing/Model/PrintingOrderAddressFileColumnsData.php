<?php

namespace App\Form\Community\Printing\Model;

use App\Entity\Community\PrintingOrder;

class PrintingOrderAddressFileColumnsData
{
    public array $columnsTypes = [];

    public static function createFromCampaign(PrintingOrder $order): self
    {
        $self = new self();
        foreach ($order->getDeliveryAddressFileFirstLines()[0] as $v) {
            $self->columnsTypes[] = 'ignored';
        }

        return $self;
    }
}
