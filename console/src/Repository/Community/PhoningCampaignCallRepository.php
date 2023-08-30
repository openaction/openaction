<?php

namespace App\Repository\Community;

use App\Entity\Community\PhoningCampaignCall;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhoningCampaignCall|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoningCampaignCall|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoningCampaignCall|null findOneByBase62Uid(string $base62Uid)
 * @method PhoningCampaignCall[]    findAll()
 * @method PhoningCampaignCall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoningCampaignCallRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoningCampaignCall::class);
    }
}
