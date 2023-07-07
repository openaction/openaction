<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\ContactUpdateRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactUpdateRepository::class)]
#[ORM\Table('community_contacts_updates')]
class ContactUpdate
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;

    public const TYPE_EMAIL = 'email';
    public const TYPE_UNREGISTER = 'unregister';

    #[ORM\Column(length: 20)]
    private string $type;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $email;

    #[ORM\Column(length: 64)]
    private string $token;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $requestedAt;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'updates')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    public function __construct(Contact $contact, ?string $email = null)
    {
        $this->uuid = Uid::random();
        $this->email = $email;
        $this->contact = $contact;
    }

    public static function createFixture(array $data): self
    {
        $self = new ContactUpdate($data['contact']);
        $self->email = $data['email'] ?? null;
        $self->type = $data['type'] ?? self::TYPE_EMAIL;
        $self->token = $data['token'] ?? bin2hex(random_bytes(32));
        $self->requestedAt = $data['requestedAt'] ?? new \DateTime();

        return $self;
    }

    public static function createEmailUpdate(Contact $contact, string $email)
    {
        $self = new self($contact, $email);

        $self->type = self::TYPE_EMAIL;
        $self->refreshToken();

        return $self;
    }

    public static function createUnregister(Contact $contact)
    {
        $self = new self($contact);

        $self->type = self::TYPE_UNREGISTER;
        $self->refreshToken();

        return $self;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRequestedAt(): \DateTime
    {
        return $this->requestedAt;
    }

    private function refreshToken()
    {
        $this->token = bin2hex(random_bytes(32));
        $this->requestedAt = new \DateTime();
    }
}
