<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Post;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Uid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post|null findOneByBase62Uid(string $base62Uid)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getAllPublicPosts(): iterable
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
                ->andWhere('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
                ->getQuery()
        );

        foreach ($iterable as $item) {
            yield $item;
            $this->_em->detach($item);
        }
    }

    public function getConsolePaginator(Project $project, ?int $category, int $currentPage, int $limit = 10): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('pc.id = :category')
                ->setParameter('category', $category);
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Paginator|Post[]
     */
    public function getApiPosts(Project $project, ?string $category, int $currentPage, int $limit = 12): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.onlyForMembers = FALSE')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('pc.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Paginator|Post[]
     */
    public function getMembersApiPosts(Project $project, ?string $category, int $currentPage, int $limit = 12): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.onlyForMembers = TRUE')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        if ($category) {
            $qb->andWhere('pc.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Post[]
     */
    public function getHomePosts(Project $project, ?int $category): iterable
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'pc', 'pi')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('p.onlyForMembers = FALSE')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(12)
        ;

        if ($category) {
            $qb->andWhere('pc.id = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

    public function getMorePosts(Post $post, int $limit = 3): iterable
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'pi')
            ->where('p.project = :project')
            ->setParameter('project', $post->getProject()->getId())
            ->andWhere('p.onlyForMembers = :onlyForMembers')
            ->setParameter('onlyForMembers', $post->isOnlyForMembers())
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->andWhere('p.id != :currentPost')
            ->setParameter('currentPost', $post->getId())
            ->setParameter('now', new \DateTime())
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function replaceImage(Post $post, Upload $upload)
    {
        // Keep reference to the old image
        $oldImage = $post->getImage();

        // Set new image
        $post->setImage($upload);
        $this->_em->persist($post);
        $this->_em->flush();

        // Remove old image (automatically removes the CDN file too using a Doctrine listener)
        if ($oldImage) {
            $this->_em->remove($oldImage);
            $this->_em->flush();
        }
    }
}
