<?php

namespace App\Repository\Website;

use App\Entity\Website\LocalizedPetition;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocalizedPetition>
 */
class LocalizedPetitionRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalizedPetition::class);
    }

    public function replaceImage(LocalizedPetition $localized, \App\Entity\Upload $upload): void
    {
        $oldImage = $localized->getImage();
        $localized->setImage($upload);
        $this->_em->persist($localized);
        $this->_em->flush();

        if ($oldImage) {
            $this->_em->remove($oldImage);
            $this->_em->flush();
        }
    }
}
