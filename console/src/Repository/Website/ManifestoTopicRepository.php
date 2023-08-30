<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\ManifestoTopic;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ManifestoTopic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ManifestoTopic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ManifestoTopic|null findOneByBase62Uid(string $base62Uid)
 * @method ManifestoTopic[]    findAll()
 * @method ManifestoTopic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManifestoTopicRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManifestoTopic::class);
    }

    public function getConsoleTopics(Project $project): iterable
    {
        return $this->createQueryBuilder('t')
            ->select('t', 'ti', 'tp')
            ->leftJoin('t.image', 'ti')
            ->leftJoin('t.proposals', 'tp')
            ->where('t.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('t.weight', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return ManifestoTopic[]
     */
    public function getApiTopics(Project $project): iterable
    {
        return $this->createQueryBuilder('t')
            ->select('t', 'ti', 'tp')
            ->leftJoin('t.image', 'ti')
            ->leftJoin('t.proposals', 'tp')
            ->where('t.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('t.weight', 'ASC')
            ->andWhere('t.publishedAt IS NOT NULL')
            ->andWhere('t.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTopicNextTo(ManifestoTopic $topic, string $type): ?ManifestoTopic
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t', 'i')
            ->leftJoin('t.image', 'i')
            ->where('t.project = :project')
            ->setParameter('project', $topic->getProject()->getId())
            ->andWhere('t.publishedAt IS NOT NULL')
            ->andWhere('t.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->setParameter('position', $topic->getWeight())
            ->setMaxResults(1)
        ;

        if ('previous' === $type) {
            $qb->orderBy('t.weight', 'DESC');
            $qb->andWhere('t.weight < :position');
        } else {
            $qb->orderBy('t.weight', 'ASC');
            $qb->andWhere('t.weight > :position');
        }

        return $qb->getQuery()->getOneOrNullResult();
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

    public function replaceImage(ManifestoTopic $topic, Upload $upload)
    {
        // Keep reference to the old image
        $oldImage = $topic->getImage();

        // Set new image
        $topic->setImage($upload);
        $this->_em->persist($topic);
        $this->_em->flush();

        // Remove old image (automatically removes the CDN file too using a Doctrine listener)
        if ($oldImage) {
            $this->_em->remove($oldImage);
            $this->_em->flush();
        }
    }
}
