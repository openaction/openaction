<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserVisit[]    findAll()
 * @method UserVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserVisit::class);
    }

    public function trackPageView(User $user)
    {
        $db = $this->_em->getConnection();

        $today = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Check if there is a row already for the day
        $result = $db->prepare('SELECT * FROM users_visits WHERE date = ? AND owner_id = ?')
            ->executeQuery([$today, $user->getId()]);

        // Insert a new row or update the existing one
        if ($day = $result->fetchAssociative()) {
            $db->executeStatement('UPDATE users_visits SET page_views = page_views + 1 WHERE id = ?', [$day['id']]);
        } else {
            $db->executeStatement(
                'INSERT INTO users_visits (id, owner_id, date, page_views) VALUES (nextval(\'users_visits_id_seq\'), ?, ?, ?)',
                [$user->getId(), $today, 1]
            );
        }
    }

    public function findTopActiveUsers(int $limit = 30): array
    {
        return $this->createQueryBuilder('v')
            ->select('u.id', 'u.firstName', 'u.lastName', 'SUM(v.pageViews) AS totalViews')
            ->leftJoin('v.owner', 'u')
            ->andWhere('v.date >= :oneMonthAgo')
            ->setParameter('oneMonthAgo', new \DateTime('-30 days'))
            ->orderBy('totalViews', 'DESC')
            ->groupBy('u.id')
            ->addGroupBy('u.firstName')
            ->addGroupBy('u.lastName')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function createAdminGraph(): array
    {
        return $this->formatGraphData(
            $this->createQueryBuilder('v')
                ->select('DATE_FORMAT(v.date, \'YYYY-MM-dd\') AS date, v.pageViews')
                ->andWhere('v.date >= :oneMonthAgo')
                ->setParameter('oneMonthAgo', new \DateTime('-30 days'))
                ->orderBy('date', 'ASC')
                ->getQuery()
                ->getArrayResult()
        );
    }

    private function formatGraphData(array $rawData): array
    {
        $stats = [];
        foreach ($rawData as $item) {
            $stats[$item['date']] = $item['pageViews'];
        }

        $date = new \DateTime('-30 days');
        $now = new \DateTime();

        $chart = [];
        while ($date < $now) {
            $chart[] = [
                't' => $date->format('Y-m-d'),
                'y' => $stats[$date->format('Y-m-d')] ?? 0,
            ];

            $date->add(\DateInterval::createFromDateString('1 day'));
        }

        return $chart;
    }
}
