<?php

namespace App\Repository\Integration;

use App\Entity\Integration\TelegramApp;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TelegramApp|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelegramApp|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelegramApp|null findOneByBase62Uid(string $base62Uid)
 * @method TelegramApp[]    findAll()
 * @method TelegramApp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelegramAppRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramApp::class);
    }
}
