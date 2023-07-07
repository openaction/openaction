<?php

namespace App\Repository\Theme;

use App\Entity\Project;
use App\Entity\Theme\ProjectAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectAsset|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectAsset|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectAsset[]    findAll()
 * @method ProjectAsset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectAsset::class);
    }

    /**
     * @return ProjectAsset[]
     */
    public function findByProject(Project $project): iterable
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'f')
            ->leftJoin('a.file', 'f')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
