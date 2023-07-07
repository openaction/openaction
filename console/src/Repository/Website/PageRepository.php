<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Page;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Uid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page|null findOneByBase62Uid(string $base62Uid)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function getAllPublicPages(): iterable
    {
        $iterable = new Paginator(
            $this->createQueryBuilder('p')
                ->select('p', 'pc', 'pi', 'po', 'o')
                ->leftJoin('p.categories', 'pc')
                ->leftJoin('p.image', 'pi')
                ->leftJoin('p.project', 'po')
                ->leftJoin('po.organization', 'o')
                ->andWhere('p.onlyForMembers = FALSE')
                ->andWhere('po.rootDomain IS NOT NULL')
                ->andWhere('po.websiteAccessUser IS NULL')
                ->getQuery()
        );

        foreach ($iterable as $item) {
            yield $item;
            $this->_em->detach($item);
        }
    }

    public function getPaginator(Project $project, ?int $category, int $currentPage, int $limit = 10): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'pc', 'pi')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('p.title', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('pc.id = :category')->setParameter('category', $category);
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Page[]
     */
    public function getApiPages(Project $project, ?string $category): iterable
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'pc', 'pi')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.onlyForMembers = FALSE')
            ->orderBy('p.title', 'ASC')
        ;

        if ($category) {
            $qb->andWhere('pc.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Page[]
     */
    public function getMembersApiPages(Project $project, ?string $category): iterable
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'pc', 'pi')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.onlyForMembers = TRUE')
            ->orderBy('p.title', 'ASC')
        ;

        if ($category) {
            $qb->andWhere('pc.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return $qb->getQuery()->getResult();
    }

    public function replaceImage(Page $page, Upload $upload)
    {
        // Keep reference to the old image
        $oldImage = $page->getImage();

        // Set new image
        $page->setImage($upload);
        $this->_em->persist($page);
        $this->_em->flush();

        // Remove old image (automatically removes the CDN file too using a Doctrine listener)
        if ($oldImage) {
            $this->_em->remove($oldImage);
            $this->_em->flush();
        }
    }
}
