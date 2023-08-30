<?php

namespace App\Repository\Website;

use App\Entity\Website\ManifestoProposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ManifestoProposal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ManifestoProposal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ManifestoProposal[]    findAll()
 * @method ManifestoProposal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManifestoProposalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManifestoProposal::class);
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
