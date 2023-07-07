<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\EmailAutomationMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailAutomationMessageRepository::class)]
#[ORM\Table('community_email_automations_messages')]
class EmailAutomationMessage
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: EmailAutomation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EmailAutomation $automation;

    #[ORM\Column(length: 250)]
    private string $email;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $formalTitle;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $firstName;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $middleName;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $lastName;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt;

    public function __construct(
        EmailAutomation $automation,
        string $email,
        string $formalTitle = null,
        string $firstName = null,
        string $middleName = null,
        string $lastName = null
    ) {
        $this->automation = $automation;
        $this->email = $email;
        $this->formalTitle = $formalTitle;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->sentAt = new \DateTime();
    }

    public static function createFixture(array $data): self
    {
        return new self(
            $data['automation'],
            $data['email'],
            $data['formalTitle'] ?? null,
            $data['firstName'] ?? null,
            $data['middleName'] ?? null,
            $data['lastName'] ?? null
        );
    }

    public static function createFromContact(EmailAutomation $automation, Contact $contact): self
    {
        return new self(
            $automation,
            $contact->getEmail(),
            $contact->getProfileFormalTitle(),
            $contact->getProfileFirstName(),
            $contact->getProfileMiddleName(),
            $contact->getProfileLastName(),
        );
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function getAutomation(): EmailAutomation
    {
        return $this->automation;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFormalTitle(): ?string
    {
        return $this->formalTitle;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
