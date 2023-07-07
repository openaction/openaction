<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\Document;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document|null findOneByBase62Uid(string $base62Uid)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * @return Document[]|Paginator
     */
    public function getProjectDocuments(Project $project, int $page, int $limit = 30): Paginator
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->where('d.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Document[]
     */
    public function getApiDocuments(Project $project): iterable
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->where('d.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('d.onlyForMembers = FALSE')
            ->orderBy('d.createdAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }
}
