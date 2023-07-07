<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\Redirection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RedirectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Redirection::class);
    }

    /**
     * @return Redirection[]
     */
    public function getProjectRedirections(Project $project): iterable
    {
        return $this->findBy(['project' => $project], ['weight' => 'ASC']);
    }

    public function getApiRedirections(Project $project): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.source', 'r.target', 'r.code')
            ->where('r.project = :project')
            ->setParameter('project', $project)
            ->orderBy('r.weight', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
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
