<?php

namespace App\Entity\Model;

class PartnerMenuItem
{
    private string $label;
    private string $url;

    public function __construct(string $label, string $url)
    {
        $this->label = $label;
        $this->url = $url;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['label'], $data['url']);
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
        ];
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
