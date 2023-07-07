<?php

namespace App\Entity\Analytics\Community;

use App\Entity\Community\Contact;
use App\Entity\Util;
use App\Repository\Analytics\Community\ContactCreationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactCreationRepository::class)]
#[ORM\Table(name: 'analytics_community_contact_creations')]
class ContactCreation
{
    use Util\EntityIdTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityProjectTrait;

    #[ORM\ManyToOne(targetEntity: Contact::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'boolean')]
    private bool $isMember;

    #[ORM\Column(type: 'boolean')]
    private bool $hasPhone;

    #[ORM\Column(type: 'boolean')]
    private bool $receivesNewsletter;

    #[ORM\Column(type: 'boolean')]
    private bool $receivesSms;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $tags = [];

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $gender;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function isMember(): bool
    {
        return $this->isMember;
    }

    public function isReceivesNewsletter(): bool
    {
        return $this->receivesNewsletter;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
