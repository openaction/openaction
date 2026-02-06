<?php

namespace App\Entity\Analytics\Website;

use App\Entity\Util;
use App\Repository\Analytics\Website\SessionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'analytics_website_sessions')]
#[ORM\Index(name: 'analytics_website_sessions_start_date', columns: ['start_date'])]
class Session
{
    use Util\EntityIdTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityProjectTrait;

    #[ORM\Column(type: 'uuid')]
    private Uuid $hash;

    #[ORM\Column(type: 'json')]
    private array $pathsFlow = [];

    #[ORM\Column(type: 'integer')]
    private int $pathsCount;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $platform;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $browser;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $originalReferrer;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmSource;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmMedium;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmCampaign;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmContent;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $startDate;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $endDate;

    public function getHash(): Uuid
    {
        return $this->hash;
    }

    public function getPathsCount(): int
    {
        return $this->pathsCount;
    }

    public function getPathsFlow(): array
    {
        return $this->pathsFlow;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getOriginalReferrer(): ?string
    {
        return $this->originalReferrer;
    }

    public function getUtmSource(): ?string
    {
        return $this->utmSource;
    }

    public function getUtmMedium(): ?string
    {
        return $this->utmMedium;
    }

    public function getUtmCampaign(): ?string
    {
        return $this->utmCampaign;
    }

    public function getUtmContent(): ?string
    {
        return $this->utmContent;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }
}
