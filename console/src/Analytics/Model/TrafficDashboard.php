<?php

namespace App\Analytics\Model;

class TrafficDashboard
{
    private array $totals;
    private array $traffic;
    private array $pages;
    private array $sources;
    private array $countries;
    private array $countriesRaw;
    private array $platforms;
    private array $platformsRaw;
    private array $browsers;
    private array $browsersRaw;
    private array $utmSources;
    private array $utmMedium;
    private array $utmCampaign;
    private array $utmContent;
    private array $events;

    public function __construct(
        array $totals,
        array $traffic,
        array $pages,
        array $sources,
        array $countries,
        array $countriesRaw,
        array $platforms,
        array $platformsRaw,
        array $browsers,
        array $browsersRaw,
        array $utmSources,
        array $utmMedium,
        array $utmCampaign,
        array $utmContent,
        array $events,
    ) {
        $this->totals = $totals;
        $this->traffic = $traffic;
        $this->pages = $pages;
        $this->sources = $sources;
        $this->countries = $countries;
        $this->countriesRaw = $countriesRaw;
        $this->platforms = $platforms;
        $this->platformsRaw = $platformsRaw;
        $this->browsers = $browsers;
        $this->browsersRaw = $browsersRaw;
        $this->utmSources = $utmSources;
        $this->utmMedium = $utmMedium;
        $this->utmCampaign = $utmCampaign;
        $this->utmContent = $utmContent;
        $this->events = $events;
    }

    public function getTotals(): array
    {
        return $this->totals;
    }

    public function getTraffic(): array
    {
        return $this->traffic;
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getSources(): array
    {
        return $this->sources;
    }

    public function getCountries(): array
    {
        return $this->countries;
    }

    public function getCountriesRaw(): array
    {
        return $this->countriesRaw;
    }

    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    public function getPlatformsRaw(): array
    {
        return $this->platformsRaw;
    }

    public function getBrowsers(): array
    {
        return $this->browsers;
    }

    public function getBrowsersRaw(): array
    {
        return $this->browsersRaw;
    }

    public function getUtmSources(): array
    {
        return $this->utmSources;
    }

    public function getUtmMedium(): array
    {
        return $this->utmMedium;
    }

    public function getUtmCampaign(): array
    {
        return $this->utmCampaign;
    }

    public function getUtmContent(): array
    {
        return $this->utmContent;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
