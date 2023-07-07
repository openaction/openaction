<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\OrganizationMainTag;
use App\Form\Community\Model\MainTagsData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrganizationMainTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationMainTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationMainTag[]    findAll()
 * @method OrganizationMainTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationMainTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationMainTag::class);
    }

    public function updateMainTags(Organization $organization, MainTagsData $data)
    {
        $this->_em->wrapInTransaction(function () use ($organization, $data) {
            // Remove previous main tags
            $this->createQueryBuilder('m')
                ->delete()
                ->where('m.organization = :orga')
                ->setParameter('orga', $organization)
                ->getQuery()
                ->execute()
            ;

            $organization->getMainTags()->clear();

            // Add new main tags
            $weight = 1;
            foreach ($data->tags as $tag) {
                if (!$tag) {
                    continue;
                }

                $mainTag = new OrganizationMainTag($organization, $tag, $weight);
                ++$weight;

                $organization->getMainTags()->add($mainTag);
                $this->_em->persist($mainTag);
            }

            // Update progress option
            $organization->setMainTagsIsProgress($data->isProgress);

            $this->_em->persist($organization);
            $this->_em->flush();
        });
    }
}
