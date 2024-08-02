<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Event;
use App\Entity\Website\Post;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Uid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrombinoscopePerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrombinoscopePerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrombinoscopePerson|null findOneByBase62Uid(string $base62Uid)
 * @method TrombinoscopePerson[]    findAll()
 * @method TrombinoscopePerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrombinoscopePersonRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrombinoscopePerson::class);
    }

    public function getConsolePersons(Project $project): iterable
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'i')
            ->leftJoin('p.image', 'i')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('p.weight', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return TrombinoscopePerson[]|array
     */
    public function getProjectPersonsList(Project $project, $hydrationMode = AbstractQuery::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('p');

        if (AbstractQuery::HYDRATE_OBJECT !== $hydrationMode) {
            $qb->select('p.id', 'p.fullName');
        }

        $qb
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('p.fullName')
        ;

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @return TrombinoscopePerson[]
     */
    public function getApiPersons(Project $project, ?string $category = null): iterable
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'i')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.image', 'i')
            ->where('p.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('p.weight', 'ASC')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
        ;

        if ($category) {
            $qb->andWhere('pc.uuid = :category')
                ->setParameter('category', Uid::fromBase62($category));
        }

        return $qb->getQuery()->getResult();
    }

    public function getPersonsNextTo(TrombinoscopePerson $person, string $type): ?TrombinoscopePerson
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'i')
            ->leftJoin('p.image', 'i')
            ->where('p.project = :project')
            ->setParameter('project', $person->getProject()->getId())
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->setParameter('position', $person->getWeight())
            ->setMaxResults(1)
        ;

        if ('previous' === $type) {
            $qb->orderBy('p.weight', 'DESC');
            $qb->andWhere('p.weight < :position');
        } else {
            $qb->orderBy('p.weight', 'ASC');
            $qb->andWhere('p.weight > :position');
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function replaceImage(TrombinoscopePerson $person, Upload $upload)
    {
        // Keep reference to the old image
        $oldImage = $person->getImage();

        // Set new image
        $person->setImage($upload);
        $this->_em->persist($person);
        $this->_em->flush();

        // Remove old image (automatically removes the CDN file too using a Doctrine listener)
        if ($oldImage) {
            $this->_em->remove($oldImage);
            $this->_em->flush();
        }
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

    public function updateAuthors(Post $post, array $authorsIds)
    {
        $this->_em->wrapInTransaction(function () use ($post, $authorsIds) {
            $metadata = $this->_em->getClassMetadata(TrombinoscopePerson::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['posts']['joinTable']['name'])
                ->where('post_id = :post')
                ->setParameter('post', $post->getId())
                ->execute()
            ;

            $post->getAuthors()->clear();
            foreach ($authorsIds as $id) {
                $post->getAuthors()->add($this->find($id));
            }

            $this->_em->persist($post);
            $this->_em->flush();
        });
    }

    public function updateParticipants(Event $event, array $participantsIds)
    {
        $this->_em->wrapInTransaction(function () use ($event, $participantsIds) {
            $metadata = $this->_em->getClassMetadata(TrombinoscopePerson::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['events']['joinTable']['name'])
                ->where('event_id = :event')
                ->setParameter('event', $event->getId())
                ->execute()
            ;

            $event->getParticipants()->clear();
            foreach ($participantsIds as $id) {
                $event->getParticipants()->add($this->find($id));
            }

            $this->_em->persist($event);
            $this->_em->flush();
        });
    }
}
