<?php

namespace App\Community;

use App\Entity\Area;
use App\Entity\Community\Contact;
use App\Repository\AreaRepository;

class ContactLocator
{
    private AreaRepository $repository;

    public function __construct(AreaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findContactCountry(string $country): ?Area
    {
        return $this->repository->searchCountry($country);
    }

    public function findContactArea(Contact $contact): ?Area
    {
        if (!$country = $contact->getAddressCountry()) {
            return null;
        }

        if (!$contact->getAddressZipCode()) {
            return $country;
        }

        return $this->repository->searchZipCode($country, $contact->getAddressZipCode()) ?: $country;
    }
}
