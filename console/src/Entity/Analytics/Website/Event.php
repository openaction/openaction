<?php

namespace App\Entity\Analytics\Website;

use App\Entity\Util;
use App\Repository\Analytics\Website\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'analytics_website_events')]
#[ORM\Index(name: 'analytics_events_hash', columns: ['hash'])]
class Event
{
    use Util\EntityIdTrait;
    use Util\EntityProjectTrait;

    #[ORM\Column(type: 'uuid')]
    private Uuid $hash;

    #[ORM\Column(length: 250)]
    private string $name;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    public function getHash(): Uuid
    {
        return $this->hash;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
