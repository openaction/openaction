<?php

namespace App\Repository\Community;

use App\Entity\Community\Ambiguity;
use App\Entity\Community\EmailingCampaignMessage;
use App\Entity\Community\PhoningCampaignCall;
use App\Entity\Community\PhoningCampaignTarget;
use App\Entity\Community\TextingCampaignMessage;
use App\Entity\Organization;
use App\Entity\Website\FormAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ambiguity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ambiguity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ambiguity[]    findAll()
 * @method Ambiguity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmbiguityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ambiguity::class);
    }

    /**
     * @return Ambiguity[] Returns an array of Ambiguity objects
     */
    public function findByOrganization(Organization $organization): iterable
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.organization = :organization')
            ->setParameter('organization', $organization->getId())
            ->andWhere('a.ignoredAt IS NULL')
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    public function applyUpdateToContactNewest(Ambiguity $ambiguity)
    {
        $newest = $ambiguity->getNewest();
        $oldest = $ambiguity->getOldest();

        $this->_em->remove($newest);
        $this->_em->persist($oldest);
        $this->_em->remove($ambiguity);

        $this->_em->flush();
    }

    public function removeRelationshipsToContactNewest(Ambiguity $ambiguity)
    {
        $newest = $ambiguity->getNewest();
        $oldest = $ambiguity->getOldest();

        $entityUpdates = [
            EmailingCampaignMessage::class,
            [PhoningCampaignCall::class, 'author'],
            PhoningCampaignTarget::class,
            TextingCampaignMessage::class,
            FormAnswer::class,
        ];

        foreach ($entityUpdates as $entity) {
            $column = 'contact';
            if (is_array($entity)) {
                [$entity, $column] = $entity;
            }

            $qb = $this->_em->createQueryBuilder()
                ->update($entity, 't')
                ->set('t.'.$column, ':oldContact')
                ->where('t.'.$column.' = :newContact')
                ->setParameter('oldContact', $oldest)
                ->setParameter('newContact', $newest)
            ;

            $qb->getQuery()->execute();
        }
    }
}
