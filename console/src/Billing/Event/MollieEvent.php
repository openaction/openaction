<?php

namespace App\Billing\Event;

use App\Entity\Billing\Order;
use Mollie\Api\Resources\Order as MollieOrder;

class MollieEvent
{
    private Order $order;
    private MollieOrder $mollieOrder;

    public function __construct(Order $order, MollieOrder $mollieOrder)
    {
        $this->order = $order;
        $this->mollieOrder = $mollieOrder;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getMollieOrder(): MollieOrder
    {
        return $this->mollieOrder;
    }
}
