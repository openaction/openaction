<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\Petition;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Uid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @extends ServiceEntityRepository<Petition>
 */
class PetitionRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Petition::class);
    }

    public function getConsolePaginator(Project $project, ?string $query, ?int $category, int $currentPage, int $limit = 10): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'lp')
            ->leftJoin('p.localizations', 'lp')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($query) {
            $qb->andWhere('LOWER(lp.title) LIKE :searchQuery OR LOWER(p.slug) LIKE :searchQuery')
                ->setParameter('searchQuery', '%'.u($query)->lower()->replace(' ', '%').'%');
        }

        // Category filter: match any localization with the category
        if ($category) {
            $qb->leftJoin('lp.categories', 'lpc')
                ->andWhere('lpc.id = :category')
                ->setParameter('category', $category);
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Petition[]
     */
    public function getApiPetitions(Project $project, ?string $category): iterable
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'lp', 'lpc')
            ->leftJoin('p.localizations', 'lp')
            ->leftJoin('lp.categories', 'lpc')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.onlyForMembers = FALSE')
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.updatedAt', 'DESC')
        ;

        if ($category) {
            // Filter petitions that have at least one localized with given category uuid
            $qb->andWhere('lpc.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneBySlug(Project $project, string $slug): ?Petition
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'lp', 'lpc')
            ->leftJoin('p.localizations', 'lp')
            ->leftJoin('lp.categories', 'lpc')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
