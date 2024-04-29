<?php

namespace App\Repository\Website;

use App\Entity\Upload;
use App\Entity\Website\PetitionLocalized;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetitionLocalized>
 *
 * @method PetitionLocalized|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetitionLocalized|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetitionLocalized[]    findAll()
 * @method PetitionLocalized[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetitionLocalizedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetitionLocalized::class);
    }

    public function save(PetitionLocalized $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PetitionLocalized $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function replaceImage(PetitionLocalized $entity, Upload $upload): void
    {
        // Keep reference to the old image
        $oldImage = $entity->getImage();

        // Set new image
        $entity->setImage($upload);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        // Remove old image (automatically removes the CDN file too using a Doctrine listener)
        if ($oldImage) {
            $this->getEntityManager()->remove($oldImage);
            $this->getEntityManager()->flush();
        }
    }
}
