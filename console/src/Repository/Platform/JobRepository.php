<?php

namespace App\Repository\Platform;

use App\Entity\Platform\Job;
use App\Util\Json;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function startJob(string $type, int $step, int $total): Job
    {
        $job = new Job($type, $step, $total);

        $this->_em->persist($job);
        $this->_em->flush();

        return $job;
    }

    public function getJobTotalSteps(int $jobId): int
    {
        return $this->createQueryBuilder('j')
            ->select('j.total')
            ->where('j.id = :jobId')
            ->setParameter('jobId', $jobId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function resetJob(int $jobId): void
    {
        $this->_em->getConnection()->executeStatement(
            sql: 'UPDATE platform_jobs SET step = 0 WHERE id = ?',
            params: [$jobId],
        );
    }

    public function setJobStatus(int $jobId, int $step, int $total): void
    {
        $this->_em->getConnection()->executeStatement(
            sql: 'UPDATE platform_jobs SET step = ?, total = ? WHERE id = ?',
            params: [$step, $total, $jobId],
        );
    }

    public function setJobStep(int $jobId, int $step, array $payload = []): void
    {
        $this->_em->getConnection()->executeStatement(
            sql: 'UPDATE platform_jobs SET step = ?, payload = ? WHERE id = ?',
            params: [$step, Json::encode($payload), $jobId],
        );
    }

    public function advanceJobStep(int $jobId, int $amount = 1, array $payload = []): void
    {
        $this->_em->getConnection()->executeStatement(
            sql: 'UPDATE platform_jobs SET step = step + '.$amount.', payload = ? WHERE id = ?',
            params: [Json::encode($payload), $jobId],
        );
    }

    public function finishJob(int $jobId, array $payload = []): void
    {
        $this->_em->getConnection()->executeStatement(
            sql: 'UPDATE platform_jobs SET total = GREATEST(1, total), step = GREATEST(1, total), payload = ? WHERE id = ?',
            params: [Json::encode($payload), $jobId],
        );
    }
}
