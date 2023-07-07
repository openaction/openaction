<?php

namespace App\Repository\Theme;

use App\Entity\Organization;
use App\Entity\Theme\WebsiteTheme;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WebsiteTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebsiteTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebsiteTheme[]    findAll()
 * @method WebsiteTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteTheme::class);
    }

    public function findByAuthor(User $author): iterable
    {
        return $this->createQueryBuilder('t')
            ->select('t', 'o')
            ->leftJoin('t.forOrganizations', 'o')
            ->where('t.author = :author')
            ->setParameter('author', $author)
            ->orderBy('t.repositoryFullName', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function updateOrganizations(WebsiteTheme $theme, array $organizationsIds)
    {
        $this->_em->wrapInTransaction(function () use ($theme, $organizationsIds) {
            $metadata = $this->_em->getClassMetadata(WebsiteTheme::class);

            // Clear old orgas
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['forOrganizations']['joinTable']['name'])
                ->where('website_theme_id = :theme')
                ->setParameter('theme', $theme->getId())
                ->execute()
            ;

            // Create new orgas
            $theme->getForOrganizations()->clear();
            foreach ($organizationsIds as $id) {
                if (!$orga = $this->_em->find(Organization::class, $id)) {
                    continue;
                }

                $theme->getForOrganizations()->add($orga);
                $orga->getWebsiteThemes()->add($theme);

                $this->_em->persist($orga);
            }

            $this->_em->persist($theme);
            $this->_em->flush();
        });
    }

    public function createChoiceQueryBuilder(Organization $orga): QueryBuilder
    {
        $accessibleEveryoneQuery = $this->createQueryBuilder('ste')
            ->select('ste.id')
            ->leftJoin('ste.author', 'stea')
            ->where('SIZE(ste.forOrganizations) = 0')
            ->andWhere('stea.isAdmin = TRUE')
            ->getQuery()
            ->getDQL()
        ;

        $accessibleOrgaQuery = $this->createQueryBuilder('sto')
            ->select('sto.id')
            ->leftJoin('sto.forOrganizations', 'soo')
            ->where('soo.id = :orga')
            ->getQuery()
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder('t');

        return $qb->where('t.repositoryFullName IS NOT NULL')
            ->andWhere('t.updateError IS NULL OR t.updateError = \'\'')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->in('t.id', $accessibleEveryoneQuery),
                $qb->expr()->in('t.id', $accessibleOrgaQuery)
            ))
            ->setParameter('orga', $orga)
            ->orderBy('t.repositoryFullName', 'ASC')
        ;
    }

    public function linkAuthor(string $installationId, User $author)
    {
        $this->createQueryBuilder('t')
            ->update()
            ->set('t.author', $author->getId())
            ->where('t.installationId = :installationId')
            ->setParameter('installationId', $installationId)
            ->andWhere('t.author IS NULL')
            ->getQuery()
            ->execute()
        ;
    }
}
