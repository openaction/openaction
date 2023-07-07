<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\PageBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageBlock[]    findAll()
 * @method PageBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageBlock::class);
    }

    /**
     * @return PageBlock[]
     */
    public function getProjectBlocks(Project $project, string $page): iterable
    {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('b.page = :page')
            ->setParameter('page', $page)
            ->orderBy('b.weight', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PageBlock[]
     */
    public function getApiBlocks(Project $project, string $page): iterable
    {
        return $this->getProjectBlocks($project, $page);
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

                $connection->update($tableName, ['weight' => (int) $item['order']], ['id' => $item['id']]);
            }
        });
    }
}
