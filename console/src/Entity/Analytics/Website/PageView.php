<?php

namespace App\Entity\Analytics\Website;

use App\Entity\Util;
use App\Repository\Analytics\Website\PageViewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PageViewRepository::class)]
#[ORM\Table(name: 'analytics_website_page_views')]
#[ORM\Index(name: 'analytics_page_views_hash', columns: ['hash'])]
class PageView
{
    use Util\EntityIdTrait;
    use Util\EntityProjectTrait;

    #[ORM\Column(type: 'uuid')]
    private Uuid $hash;

    #[ORM\Column(length: 250)]
    private string $path;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $platform;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $browser;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $referrer;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $referrerPath;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmSource;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmMedium;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmCampaign;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $utmContent;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    public function getHash(): Uuid
    {
        return $this->hash;
    }

    public function getPath(): string
    {
        return $this->path;
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

    public function getReferrer(): ?string
    {
        return $this->referrer;
    }

    public function getReferrerPath(): ?string
    {
        return $this->referrerPath;
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

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
