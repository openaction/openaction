<?php

namespace App\Repository\Integration;

use App\Entity\Integration\TelegramApp;
use App\Entity\Integration\TelegramAppAuthorization;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TelegramAppAuthorization|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelegramAppAuthorization|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelegramAppAuthorization[]    findAll()
 * @method TelegramAppAuthorization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelegramAppAuthorizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramAppAuthorization::class);
    }

    public function findAuthorization(TelegramApp $app, User $user): ?TelegramAppAuthorization
    {
        return $this->findOneBy(['app' => $app, 'member' => $user]);
    }
}
