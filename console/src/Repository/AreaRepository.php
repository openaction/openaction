<?php

namespace App\Repository;

use App\Entity\Area;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @method Area|null find($id, $lockMode = null, $lockVersion = null)
 * @method Area|null findOneBy(array $criteria, array $orderBy = null)
 * @method Area[]    findAll()
 * @method Area[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Area::class);
    }

    /**
     * @return Area[]
     */
    public function findFilter(array $areasIds): array
    {
        if (!$areasIds) {
            return [];
        }

        $qb = $this->createQueryBuilder('a');

        return $qb->select('a')
            ->where($qb->expr()->in('a.id', $areasIds))
            ->getQuery()
            ->getResult()
        ;
    }

    public function findRawChildrenOf(?int $id): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id', 'a.name', 'a.description')
            ->orderBy('a.name', 'ASC')
        ;

        if (!$id) {
            $qb->where('a.parent IS NULL');
        } else {
            $qb->where('a.parent = :parent')->setParameter('parent', $id);
        }

        $areas = [];
        foreach ($qb->getQuery()->toIterable([], Query::HYDRATE_ARRAY) as $area) {
            $areas[$area['id']] = [
                'name' => $area['name'],
                'desc' => $area['description'],
            ];
        }

        return $areas;
    }

    public function findAllCountries()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :type')
            ->setParameter('type', Area::TYPE_COUNTRY)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findChildrenByType(Area $parent, string $areaType): array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('a.id', 'a.name')
            ->where('a.treeLeft > :parentLeft')
            ->setParameter('parentLeft', $parent->getTreeLeft())
            ->andWhere('a.treeRight < :parentRight')
            ->setParameter('parentRight', $parent->getTreeRight())
            ->andWhere('a.type = :type')
            ->setParameter('type', $areaType)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function searchCountry(string $query): ?Area
    {
        $term = u($query)->lower();

        $qb = $this->createQueryBuilder('a');
        $qb->setParameter('equalsTerm', $term->toString());
        $qb->setParameter('startWithTerm', $term->toString().'%');

        $scoreSelect = '(
            (CASE WHEN LOWER(a.code) = :equalsTerm THEN 256 ELSE 0 END) +
            (CASE WHEN LOWER(a.name) = :equalsTerm THEN 128 ELSE 0 END) +
            (CASE WHEN LOWER(a.code) LIKE :startWithTerm THEN 16 ELSE 0 END) +
            (CASE WHEN LOWER(a.name) LIKE :startWithTerm THEN 8 ELSE 0 END)
        )';

        $qb->orderBy($scoreSelect, 'DESC');
        $qb->where($scoreSelect.' > 0');
        $qb->andWhere('a.type = :type');
        $qb->setParameter('type', Area::TYPE_COUNTRY);

        $qb->addOrderBy('a.name', 'ASC');
        $qb->setMaxResults(5);

        $areas = $qb->getQuery()->getResult();

        return $areas ? $areas[0] : null;
    }

    public function searchZipCode(Area $country, string $query): ?Area
    {
        return $this->findOneBy([
            'type' => Area::TYPE_ZIP_CODE,
            'treeRoot' => $country,
            'name' => u($query)->replace(' ', '')->trim()->toString(),
        ]);
    }
}
