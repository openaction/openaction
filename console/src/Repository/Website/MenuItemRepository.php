<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\MenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenuItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuItem[]    findAll()
 * @method MenuItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuItem::class);
    }

    /**
     * @return MenuItem[]
     */
    public function getProjectMenuTree(Project $project, string $position): iterable
    {
        return $this->createQueryBuilder('m')
            ->select('m', 'mc')
            ->leftJoin('m.children', 'mc')
            ->where('m.project = :project')
            ->setParameter('project', $project)
            ->andWhere('m.position = :position')
            ->setParameter('position', $position)
            ->andWhere('m.parent IS NULL')
            ->orderBy('m.weight', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getApiProjectTree(Project $project): array
    {
        $data = $this->createQueryBuilder('m')
            ->select('m.position', 'm.id', 'm.label', 'm.url', 'm.openNewTab', 'm.weight', 'IDENTITY(m.parent) as parentId')
            ->andWhere('m.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.parent', 'DESC') // Parents first
            ->addOrderBy('m.weight', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        $tree = [];
        foreach ($data as $item) {
            $child = [
                '_resource' => 'MenuItem',
                'label' => $item['label'],
                'url' => $item['url'],
                'openNewTab' => (bool) $item['openNewTab'],
                'weight' => $item['weight'],
                'children' => ['data' => []],
            ];

            if ($item['parentId']) {
                $tree[$item['position']][$item['parentId']]['children']['data'][] = $child;
            } else {
                $tree[$item['position']][$item['id']] = $child;
            }
        }

        foreach ($tree as $position => $items) {
            foreach ($items as $id => $item) {
                $tree[$position][$id]['children']['data'] = array_values($item['children']['data']);
            }

            $tree[$position] = array_values($items);
        }

        return $tree;
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
