<?php

namespace App\Entity;

use App\Entity\Util\EntityIdTrait;
use App\Repository\UserVisitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserVisitRepository::class)]
#[ORM\Table(name: 'users_visits')]
#[ORM\Index(name: 'users_visits_owner_date', columns: ['owner_id', 'date'])]
#[ORM\Index(name: 'users_visits_owner', columns: ['owner_id'])]
#[ORM\Index(name: 'users_visits_date', columns: ['date'])]
class UserVisit
{
    use EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'visits')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Column(type: 'date')]
    private \DateTime $date;

    #[ORM\Column(type: 'bigint')]
    private int $pageViews;

    public function __construct(User $owner)
    {
        $this->owner = $owner;
        $this->date = new \DateTime();
        $this->pageViews = 0;
    }

    public function increment(): int
    {
        ++$this->pageViews;

        return $this->pageViews;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getPageViews(): int
    {
        return $this->pageViews;
    }
}
