<?php

namespace App\Community\Member;

class AuthorizationToken
{
    private string $firstName;
    private string $lastName;
    private string $nonce;
    private string $encrypted;

    public function __construct(string $firstName, string $lastName, string $nonce, string $encrypted)
    {
        $this->firstName = htmlentities($firstName, ENT_QUOTES, 'UTF-8');
        $this->lastName = htmlentities($lastName, ENT_QUOTES, 'UTF-8');
        $this->nonce = $nonce;
        $this->encrypted = $encrypted;
    }

    public static function createFromPayload(array $payload): self
    {
        if (!isset($payload['nonce'], $payload['encrypted'])) {
            throw new \InvalidArgumentException();
        }

        return new self($payload['firstName'] ?? '', $payload['lastName'] ?? '', $payload['nonce'], $payload['encrypted']);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function getEncrypted(): string
    {
        return $this->encrypted;
    }
}
