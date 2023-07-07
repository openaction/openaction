<?php

namespace App\Entity\Model;

class CloudflareDomainConfig
{
    private string $id;
    private string $name;
    private string $status;
    private array $nameservers;

    public function __construct(string $id, string $name, string $status, array $nameservers)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->nameservers = $nameservers;
    }

    public static function fromConfig(array $config): self
    {
        return new self(
            $config['id'] ?? '',
            $config['name'] ?? '',
            $config['status'] ?? 'pending',
            $config['nameservers'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'nameservers' => $this->nameservers,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return 'active' === $this->status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNameservers(): array
    {
        return $this->nameservers;
    }
}
