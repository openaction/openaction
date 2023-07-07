<?php

namespace App\Entity\Billing\Model;

use App\Entity\User;

class OrderRecipient
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $locale;

    public function __construct(?string $firstName, ?string $lastName, ?string $email, ?string $locale)
    {
        $this->firstName = $firstName ?: '';
        $this->lastName = $lastName ?: '';
        $this->email = $email ?: '';
        $this->locale = $locale ?: '';
    }

    public function __toString(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['firstName'] ?? '', $data['lastName'] ?? '', $data['email'] ?? '', $data['locale'] ?? 'fr');
    }

    public static function fromUser(User $user): self
    {
        return new self($user->getFirstName(), $user->getLastName(), $user->getEmail(), $user->getLocale());
    }

    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'locale' => $this->locale,
        ];
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
