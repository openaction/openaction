<?php

namespace App\Community;

use App\Api\Model\LoginApiData;
use App\Community\Member\AuthorizationToken;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Repository\Community\ContactRepository;
use App\Util\Json;
use App\Util\Uid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberAuthenticator
{
    public const TOKEN_HEADER = 'X-Citipo-Auth-Token';

    private string $secret;

    private ContactRepository $repository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(string $secret, ContactRepository $r, UserPasswordHasherInterface $hasher)
    {
        $this->secret = $secret;
        $this->repository = $r;
        $this->hasher = $hasher;
    }

    public function authenticate(Organization $organization, LoginApiData $data): ?Contact
    {
        if (!$contact = $this->repository->findOneByMainEmail($organization, $data->email)) {
            return null;
        }

        if (!$contact->isAccountConfirmed()) {
            return null;
        }

        if (!$this->hasher->isPasswordValid($contact, $data->password)) {
            return null;
        }

        return $contact;
    }

    public function createAuthorizationToken(Contact $contact, string $expiresAt = '+7 days'): AuthorizationToken
    {
        $payload = [
            'id' => Uid::toBase62($contact->getUuid()),
            'expiresAt' => (new \DateTime($expiresAt))->format('U'),
        ];

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = sodium_crypto_secretbox(Json::encode($payload), $nonce, $this->getSecret());

        return new AuthorizationToken(
            $contact->getProfileFirstName() ?: '',
            $contact->getProfileLastName() ?: '',
            base64_encode($nonce),
            base64_encode($encrypted)
        );
    }

    public function authorize(AuthorizationToken $token): ?Contact
    {
        try {
            $decrypted = sodium_crypto_secretbox_open(
                base64_decode($token->getEncrypted()),
                base64_decode($token->getNonce()),
                $this->getSecret()
            );
        } catch (\SodiumException) {
            return null;
        }

        if (!$decrypted) {
            return null;
        }

        try {
            $decoded = Json::decode($decrypted);
        } catch (\Throwable) {
            return null;
        }

        // Check if the token expired
        if ($decoded['expiresAt'] < time()) {
            return null;
        }

        return $this->repository->findOneByBase62Uid($decoded['id']);
    }

    private function getSecret(): string
    {
        return base64_decode($this->secret);
    }
}
