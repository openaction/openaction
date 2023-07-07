<?php

namespace App\Billing\Model;

class OrderLine
{
    public const TYPE_PRODUCT = 'digital';
    public const TYPE_DISCOUNT = 'discount';
    public const TYPE_SHIPPING_FEE = 'shipping_fee';

    private string $type;
    private string $name;
    private string $description;
    private int $quantity;
    private float $unitPrice;
    private float $vatRate;

    public function __construct(string $type, string $name, string $description, int $quantity, float $unitPrice, float $vatRate)
    {
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->unitPrice = round($unitPrice, 6);
        $this->vatRate = $vatRate;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['type'],
            $data['name'],
            $data['description'] ?? '',
            $data['quantity'],
            $data['unitPrice'],
            $data['vatRate'],
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice,
            'vatRate' => $this->vatRate,
        ];
    }

    public function getUnitPriceExludingTaxes(): float
    {
        return $this->unitPrice;
    }

    public function getUnitPriceIncludingTaxes(): float
    {
        return round($this->unitPrice * (1 + ($this->vatRate / 100)), 6);
    }

    public function getTotalAmountExcludingTaxes(): float
    {
        return round($this->unitPrice * $this->quantity, 6);
    }

    public function getTotalAmountIncludingTaxes(): float
    {
        return round($this->getTotalAmountExcludingTaxes() * (1 + ($this->vatRate / 100)), 6);
    }

    public function getTotalVatAmount(): float
    {
        return round($this->getTotalAmountIncludingTaxes() - $this->getTotalAmountExcludingTaxes(), 6);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getVatRate(): float
    {
        return $this->vatRate;
    }
}
