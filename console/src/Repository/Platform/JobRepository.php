<?php

namespace App\Repository\Platform;

use App\Entity\Platform\Job;
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

    public function resetJob(int $jobId)
    {
        $this->createQueryBuilder('j')
            ->update()
            ->set('j.step', '0')
            ->where('j.id = :jobId')
            ->setParameter('jobId', $jobId)
            ->getQuery()
            ->execute()
        ;
    }

    public function setJobStep(int $jobId, int $step)
    {
        $this->createQueryBuilder('j')
            ->update()
            ->set('j.step', $step)
            ->where('j.id = :jobId')
            ->setParameter('jobId', $jobId)
            ->getQuery()
            ->execute()
        ;
    }

    public function setJobTotalSteps(int $jobId, int $total)
    {
        $this->createQueryBuilder('j')
            ->update()
            ->set('j.total', $total)
            ->where('j.id = :jobId')
            ->setParameter('jobId', $jobId)
            ->getQuery()
            ->execute()
        ;
    }

    public function advanceJobStep(int $jobId, int $amount = 10)
    {
        $this->createQueryBuilder('j')
            ->update()
            ->set('j.step', 'j.step + '.$amount)
            ->where('j.id = :jobId')
            ->setParameter('jobId', $jobId)
            ->getQuery()
            ->execute()
        ;
    }

    public function finishJob(int $jobId, array $payload = [])
    {
        $job = $this->find($jobId);
        $job->finish($payload);

        $this->_em->persist($job);
        $this->_em->flush();
    }
}
