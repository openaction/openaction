<?php

namespace App\Repository\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use function Symfony\Component\String\u;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    private SluggerInterface $slugger;

    public function __construct(ManagerRegistry $registry, SluggerInterface $slugger)
    {
        parent::__construct($registry, Tag::class);

        $this->slugger = $slugger;
    }

    public function search(Organization $orga, ?string $query): array
    {
        $qb = $this->createQueryBuilder('t')->select('t.id', 't.name', 't.slug');

        if ($query) {
            $term = u($query)->lower();

            $qb->setParameter('equalsTerm', $term->toString());
            $qb->setParameter('startWithTerm', $term->toString().'%');

            $scoreSelect = '(
                (CASE WHEN LOWER(t.name) = :equalsTerm THEN 64 ELSE 0 END) +
                (CASE WHEN LOWER(t.slug) = :equalsTerm THEN 64 ELSE 0 END) +
                (CASE WHEN LOWER(t.name) LIKE :startWithTerm THEN 16 ELSE 0 END) +
                (CASE WHEN LOWER(t.slug) LIKE :startWithTerm THEN 16 ELSE 0 END)
            ';

            $keywords = $this->slugger->slug($term, '|')->split('|');
            foreach ($keywords as $k => $keyword) {
                $qb->setParameter('equalsKeyword'.$k, $keyword);
                $qb->setParameter('containsKeyword'.$k, '%'.$keyword.'%');

                $scoreSelect .= ' +
                    (CASE WHEN LOWER(t.name) = :equalsKeyword'.$k.' THEN 8 ELSE 0 END) +
                    (CASE WHEN LOWER(t.slug) = :equalsKeyword'.$k.' THEN 8 ELSE 0 END) +
                    (CASE WHEN LOWER(t.name) LIKE :containsKeyword'.$k.' THEN 4 ELSE 0 END) +
                    (CASE WHEN LOWER(t.slug) LIKE :containsKeyword'.$k.' THEN 4 ELSE 0 END)
                ';
            }

            $scoreSelect .= ')';
            $qb->orderBy($scoreSelect, 'DESC');
            $qb->where($scoreSelect.' > 0');
        }

        $qb->andWhere('t.organization = :orga');
        $qb->setParameter('orga', $orga);
        $qb->addOrderBy('t.name', 'ASC');

        $tags = [];
        foreach ($qb->getQuery()->toIterable([], Query::HYDRATE_ARRAY) as $tag) {
            $tags[$tag['id']] = [
                'name' => $tag['name'],
                'slug' => $tag['slug'],
            ];
        }

        return $tags;
    }

    /**
     * @return Tag[]
     */
    public function findAllByOrganization(Organization $organization)
    {
        return $this->createQueryBuilder('t')
            ->where('t.organization = :organization')
            ->orderBy('t.name', 'ASC')
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return string[]
     */
    public function findNamesByOrganization(Organization $organization): array
    {
        return array_column(
            $this->createQueryBuilder('t')
                ->select('t.name')
                ->where('t.organization = :organization')
                ->setParameter('organization', $organization)
                ->orderBy('t.name', 'ASC')
                ->getQuery()
                ->getArrayResult(),
            'name'
        );
    }

    public function findOneByName(Organization $organization, string $tag): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->where('t.organization = :organization')
            ->setParameter('organization', $organization)
            ->andWhere('LOWER(t.name) = :tag')
            ->setParameter('tag', u($tag)->lower()->toString())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function addTagToContactsBatch(array $contactsUuids, int $tagId)
    {
        if (!$contactsUuids) {
            return;
        }

        $this->_em->getConnection()->executeStatement('
            INSERT INTO community_contacts_tags (tag_id, contact_id) 
            SELECT '.$tagId.', c.id
            FROM community_contacts c
            WHERE c.uuid IN (\''.implode('\', \'', $contactsUuids).'\')
            ON CONFLICT DO NOTHING
        ');
    }

    public function removeTagFromContactsBatch(array $contactsUuids, int $tagId)
    {
        if (!$contactsUuids) {
            return;
        }

        $this->_em->getConnection()->executeStatement('
            DELETE FROM community_contacts_tags ct
            WHERE ct.tag_id = '.$tagId.' AND ct.contact_id IN (
                SELECT c.id FROM community_contacts c WHERE c.uuid IN (\''.implode('\', \'', $contactsUuids).'\') 
            )
        ');
    }

    public function replaceContactTags(Contact $contact, array $tagsNames)
    {
        $contact->getMetadataTags()->clear();

        // Find existing tags and create new ones
        $tags = [];
        foreach ($tagsNames as $tagName) {
            $tag = $this->findOneByName($contact->getOrganization(), $tagName);
            if (!$tag) {
                $tag = new Tag($contact->getOrganization(), $tagName);
            }

            $this->_em->persist($tag);
            $tags[] = $tag;
        }

        $this->_em->flush();
        $this->_em->clear(Tag::class);

        // Synchronize contact tags
        $this->_em->wrapInTransaction(function () use ($contact, $tags) {
            // Clear old tags
            $this->_em->getConnection()->executeStatement('
                DELETE FROM community_contacts_tags ct 
                WHERE ct.contact_id = '.$contact->getId()
            );

            // Create new tags associations
            $values = [];
            foreach ($tags as $tag) {
                $values[] = '('.$contact->getId().', '.$tag->getId().')';
            }

            if ($values) {
                $this->_em->getConnection()->executeStatement('
                    INSERT INTO community_contacts_tags (contact_id, tag_id) 
                    VALUES '.implode(', ', array_unique($values)).'
                    ON CONFLICT DO NOTHING
                ');
            }
        });
    }
}
