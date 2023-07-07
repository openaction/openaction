<?php

namespace App\Security\Provider;

use App\Entity\Integration\TelegramAppAuthorization;
use App\Repository\Integration\TelegramAppAuthorizationRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class IntegrationsApiProvider implements UserProviderInterface
{
    private TelegramAppAuthorizationRepository $telegramRepository;

    public function __construct(TelegramAppAuthorizationRepository $telegramRepository)
    {
        $this->telegramRepository = $telegramRepository;
    }

    public function supportsClass(string $class): bool
    {
        return TelegramAppAuthorization::class === $class;
    }

    /**
     * @param TelegramAppAuthorization $authorization
     */
    public function refreshUser(UserInterface $authorization): TelegramAppAuthorization
    {
        return $this->telegramRepository->find($authorization->getId());
    }

    public function loadUserByUsername(string $username): TelegramAppAuthorization
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): TelegramAppAuthorization
    {
        $authorization = $this->telegramRepository->findOneBy(['apiToken' => $identifier]);

        if (!$authorization || !$authorization->getMember()) {
            throw new UserNotFoundException();
        }

        return $authorization;
    }
}
