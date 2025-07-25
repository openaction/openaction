<?php

namespace App\Repository\Website;

use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method PostCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostCategory|null findOneByBase62Uid(string $base62Uid)
 * @method PostCategory[]    findAll()
 * @method PostCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostCategoryRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostCategory::class);
    }

    public function findProjectCategory(Project $project, int $id): ?PostCategory
    {
        return $this->findOneBy(['project' => $project, 'id' => $id]);
    }

    /**
     * @return PostCategory[]|array
     */
    public function getProjectCategories(Project $project, $hydrationMode = Query::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc')
            ->where('pc.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('pc.weight')
        ;

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @return PostCategory[]
     */
    public function getOrganizationCategories(Organization $organization): array
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc', 'p')
            ->leftJoin('pc.project', 'p')
            ->where('p.organization = :organization')
            ->setParameter('organization', $organization->getId())
            ->orderBy('pc.weight')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int[]
     */
    public function countPostsByProjectCategory(Project $project): array
    {
        $data = $this->_em->getConnection()->executeQuery('
            SELECT c.id, COUNT(*)
            FROM website_posts_posts_categories pc
            LEFT JOIN website_posts_categories c ON pc.post_category_id = c.id
            WHERE c.project_id = ?
            GROUP BY c.id
        ', [$project->getId()]);

        $counts = [];
        foreach ($data->fetchAllAssociative() as $row) {
            $counts[$row['id']] = $row['count'];
        }

        return $counts;
    }

    public function sort(array $data)
    {
        $connection = $this->_em->getConnection();
        $tableName = $this->_class->getTableName();

        $connection->transactional(static function () use ($connection, $tableName, $data) {
            foreach ($data as $item) {
                if (!isset($item['order'], $item['id'])) {
                    throw new \InvalidArgumentException('Invalid params order');
                }

                $connection->update($tableName, ['weight' => (int) $item['order']], ['uuid' => $item['id']]);
            }
        });
    }

    public function updateCategories(Post $post, array $categoriesIds)
    {
        $qb = $this->createQueryBuilder('c');

        if ($ids = array_filter($categoriesIds, fn ($id) => !Uuid::isValid($id))) {
            $qb->orWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        }

        if ($uuids = array_filter($categoriesIds, fn ($id) => Uuid::isValid($id))) {
            $qb->orWhere('c.uuid IN (:uuids)')->setParameter('uuids', $uuids);
        }

        $categories = [];
        if ($ids || $uuids) {
            $categories = $qb->getQuery()->getResult();
        }

        $this->_em->wrapInTransaction(function () use ($post, $categories) {
            $metadata = $this->_em->getClassMetadata(PostCategory::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['posts']['joinTable']['name'])
                ->where('post_id = :post')
                ->setParameter('post', $post->getId())
                ->execute()
            ;

            $post->getCategories()->clear();
            foreach ($categories as $category) {
                $post->getCategories()->add($category);
            }

            $this->_em->persist($post);
            $this->_em->flush();
        });
    }
}
