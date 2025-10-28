<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Event;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Uid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event|null findOneByBase62Uid(string $base62Uid)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function getAllPublicEvents(): iterable
    {
        $iterable = new Paginator(
            $this->createQueryBuilder('e')
                ->select('e', 'ec', 'ei', 'ep', 'o')
                ->leftJoin('e.categories', 'ec')
                ->leftJoin('e.image', 'ei')
                ->leftJoin('e.project', 'ep')
                ->leftJoin('ep.organization', 'o')
                ->andWhere('e.onlyForMembers = FALSE')
                ->andWhere('ep.rootDomain IS NOT NULL')
                ->andWhere('e.publishedAt <= :now')
                ->orderBy('e.createdAt', 'DESC')
                ->setParameter('now', new \DateTime())
                ->getQuery()
        );

        foreach ($iterable as $item) {
            yield $item;
            $this->_em->detach($item);
        }
    }

    public function getProjectPaginator(Project $project, ?int $category, int $currentPage, int $limit = 20): Paginator
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->leftJoin('e.categories', 'ec')
            ->leftJoin('e.image', 'ei')
            ->where('e.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('ec.id = :category')
                ->setParameter('category', $category);
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Event[]
     */
    public function getHomeEvents(Project $project, ?int $category): iterable
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->leftJoin('e.categories', 'ec')
            ->leftJoin('e.image', 'ei')
            ->where('e.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('e.onlyForMembers = FALSE')
            ->andWhere('e.publishedAt IS NOT NULL')
            ->andWhere('e.publishedAt <= :now')
            ->andWhere('e.beginAt >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.beginAt', 'ASC')
            ->setMaxResults(18)
        ;

        if ($category) {
            $qb->andWhere('ec.id = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Paginator|Event[]
     */
    public function getApiEvents(Project $project, ?string $category, ?string $participant, bool $archived, int $currentPage, int $limit = 12): Paginator
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->leftJoin('e.categories', 'ec')
            ->leftJoin('e.participants', 'ep')
            ->leftJoin('e.image', 'ei')
            ->where('e.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('e.onlyForMembers = FALSE')
            ->andWhere('e.publishedAt IS NOT NULL')
            ->andWhere('e.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('ec.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        if ($participant) {
            $qb->andWhere('ep.uuid = :participant')
                ->setParameter('participant', Uid::fromBase62($participant));
        }

        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        if ($archived) {
            $qb->andWhere('e.beginAt < :today')
                ->setParameter('today', $today)
                ->orderBy('e.beginAt', 'DESC');
        } else {
            $qb->andWhere('e.beginAt >= :today')
                ->setParameter('today', $today)
                ->orderBy('e.beginAt', 'ASC');
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Paginator|Event[]
     */
    public function getMembersApiEvents(Project $project, ?string $category, int $currentPage, int $limit = 12): Paginator
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->leftJoin('e.categories', 'ec')
            ->leftJoin('e.image', 'ei')
            ->where('e.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('e.onlyForMembers = TRUE')
            ->andWhere('e.publishedAt IS NOT NULL')
            ->andWhere('e.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.beginAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('ec.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return new Paginator($qb->getQuery(), true);
    }

    public function replaceImage(Event $event, Upload $upload)
    {
        // Keep reference to the old image
        $oldImage = $event->getImage();

        // Set new image
        $event->setImage($upload);
        $this->_em->persist($event);
        $this->_em->flush();

        // Remove old image (automatically removes the CDN file too using a Doctrine listener)
        if ($oldImage) {
            $this->_em->remove($oldImage);
            $this->_em->flush();
        }
    }
}
