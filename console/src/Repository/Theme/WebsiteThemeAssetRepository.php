<?php

namespace App\Repository\Theme;

use App\Entity\Theme\WebsiteTheme;
use App\Entity\Theme\WebsiteThemeAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WebsiteThemeAsset|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebsiteThemeAsset|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebsiteThemeAsset[]    findAll()
 * @method WebsiteThemeAsset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteThemeAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteThemeAsset::class);
    }

    /**
     * @return WebsiteThemeAsset[]
     */
    public function findByTheme(WebsiteTheme $theme): iterable
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'f')
            ->leftJoin('a.file', 'f')
            ->where('a.theme = :theme')
            ->setParameter('theme', $theme)
            ->orderBy('a.pathname', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
