<?php

namespace App\Entity;

use App\Entity\Util;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('membership_plans')]
#[ORM\Index(columns: ['project_id'], name: 'membership_plans_project_idx')]
class MembershipPlan
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\Column(length: 150)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $price;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 10)]
    private string $interval; // monthly, yearly

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $image = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(type: 'integer')]
    private int $sortOrder = 0;

    public function __construct(Project $project, string $name, int $price, string $currency, string $interval)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->name = $name;
        $this->price = $price;
        $this->currency = strtoupper($currency);
        $this->interval = $interval;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description ?: null;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = strtoupper($currency);
    }

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function setInterval(string $interval): void
    {
        $this->interval = $interval;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }

    public function setImage(?Upload $image): void
    {
        $this->image = $image;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }
}

