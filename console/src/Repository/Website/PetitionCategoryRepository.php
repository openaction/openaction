<?php

namespace App\Repository\Website;

use App\Entity\Website\PetitionCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetitionCategory>
 *
 * @method PetitionCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetitionCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetitionCategory[]    findAll()
 * @method PetitionCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetitionCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetitionCategory::class);
    }

    public function save(PetitionCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PetitionCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
