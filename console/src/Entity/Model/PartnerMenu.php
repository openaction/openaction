<?php

namespace App\Entity\Model;

class PartnerMenu
{
    private array $items;

    /**
     * @param PartnerMenuItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            $items[] = PartnerMenuItem::fromArray($item);
        }

        return new self($items);
    }

    public function toArray(): array
    {
        $itemsData = [];
        foreach ($this->items as $item) {
            $itemsData[] = $item->toArray();
        }

        return ['items' => $itemsData];
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
