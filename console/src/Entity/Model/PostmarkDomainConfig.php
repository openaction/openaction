<?php

namespace App\Entity\Model;

class PostmarkDomainConfig
{
    private int $id;
    private string $name;
    private bool $dkimVerified;
    private string $dkimHost;
    private string $dkimContent;
    private bool $returnPathVerified;
    private string $returnPathHost;
    private string $returnPathTarget;

    public function __construct(
        int $id,
        string $name,
        bool $dkimVerified,
        string $dkimHost,
        string $dkimContent,
        bool $returnPathVerified,
        string $returnPathHost,
        string $returnPathTarget,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->dkimVerified = $dkimVerified;
        $this->dkimHost = $dkimHost;
        $this->dkimContent = $dkimContent;
        $this->returnPathVerified = $returnPathVerified;
        $this->returnPathHost = $returnPathHost;
        $this->returnPathTarget = $returnPathTarget;
    }

    public static function fromConfig(array $config): self
    {
        return new self(
            $config['id'] ?? 0,
            $config['name'] ?? '',
            $config['dkimVerified'] ?? false,
            $config['dkimHost'] ?? '',
            $config['dkimContent'] ?? '',
            $config['returnPathVerified'] ?? false,
            $config['returnPathHost'] ?? '',
            $config['returnPathTarget'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dkimVerified' => $this->dkimVerified,
            'dkimHost' => $this->dkimHost,
            'dkimContent' => $this->dkimContent,
            'returnPathVerified' => $this->returnPathVerified,
            'returnPathHost' => $this->returnPathHost,
            'returnPathTarget' => $this->returnPathTarget,
        ];
    }

    public function isFullyVerified(): bool
    {
        return $this->dkimVerified && $this->returnPathVerified;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDkimVerified(): bool
    {
        return $this->dkimVerified;
    }

    public function getDkimHost(): string
    {
        return $this->dkimHost;
    }

    public function getDkimContent(): string
    {
        return $this->dkimContent;
    }

    public function isReturnPathVerified(): bool
    {
        return $this->returnPathVerified;
    }

    public function getReturnPathHost(): string
    {
        return $this->returnPathHost;
    }

    public function getReturnPathTarget(): string
    {
        return $this->returnPathTarget;
    }
}
