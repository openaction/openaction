<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\ContactLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactLogRepository::class)]
#[ORM\Table('community_contacts_logs')]
class ContactLog
{
    use Util\EntityIdTrait;

    public const TYPE_NEWSLETTER_SUBSCRIBE = 'newsletter_subscribe';
    public const TYPE_NEWSLETTER_UNSUBSCRIBE = 'newsletter_unsubscribe';
    public const TYPE_SMS_SUBSCRIBE = 'sms_subscribe';
    public const TYPE_SMS_UNSUBSCRIBE = 'sms_unsubscribe';
    public const TYPE_CALLS_SUBSCRIBE = 'calls_subscribe';
    public const TYPE_CALLS_UNSUBSCRIBE = 'calls_unsubscribe';

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'logs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(length: 30)]
    private string $type;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $source;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    public function __construct(Contact $contact, string $type, ?string $source = null)
    {
        $this->contact = $contact;
        $this->type = $type;
        $this->source = $source;
        $this->date = new \DateTime();
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
