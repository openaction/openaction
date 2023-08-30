<?php

namespace App\Dashboard\Model;

class PartnerDashboard
{
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return PartnerDashboardItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
