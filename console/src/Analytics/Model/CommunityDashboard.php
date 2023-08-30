<?php

namespace App\Analytics\Model;

class CommunityDashboard
{
    private array $totals;
    private array $growth;
    private array $tags;
    private array $countries;
    private array $countriesRaw;

    public function __construct(array $totals, array $growth, array $tags, array $countries, array $countriesRaw)
    {
        $this->totals = $totals;
        $this->growth = $growth;
        $this->tags = $tags;
        $this->countries = $countries;
        $this->countriesRaw = $countriesRaw;
    }

    public function getTotals(): array
    {
        return $this->totals;
    }

    public function getGrowth(): array
    {
        return $this->growth;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getCountries(): array
    {
        return $this->countries;
    }

    public function getCountriesRaw(): array
    {
        return $this->countriesRaw;
    }
}
