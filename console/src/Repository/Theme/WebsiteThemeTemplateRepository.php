<?php

namespace App\Repository\Theme;

use App\Entity\Theme\WebsiteTheme;
use App\Entity\Theme\WebsiteThemeTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WebsiteThemeTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebsiteThemeTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebsiteThemeTemplate[]    findAll()
 * @method WebsiteThemeTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteThemeTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteThemeTemplate::class);
    }

    public function deleteByTheme(WebsiteTheme $theme): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.theme = :theme')
            ->setParameter('theme', $theme)
            ->getQuery()
            ->execute()
        ;
    }
}
