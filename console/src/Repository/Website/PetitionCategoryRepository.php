<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\PetitionCategory;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetitionCategory>
 */
class PetitionCategoryRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetitionCategory::class);
    }

    /**
     * @return PetitionCategory[]|array
     */
    public function getProjectCategories(Project $project, $hydrationMode = Query::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc')
            ->where('pc.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('pc.weight')
        ;

        return $qb->getQuery()->getResult($hydrationMode);
    }
}
