<?php

namespace App\Repository\Website;

use App\Entity\Community\Contact;
use App\Entity\Website\Form;
use App\Entity\Website\FormAnswer;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Json;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormAnswer|null findOneByBase62Uid(string $base62Uid)
 * @method FormAnswer[]    findAll()
 * @method FormAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormAnswerRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormAnswer::class);
    }

    public function getPaginator(Form $form, int $currentPage, int $limit = 50): Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.form = :form')
            ->setParameter('form', $form->getId())
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        return new Paginator($qb->getQuery(), true);
    }

    public function getExportData(Form $form): iterable
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT
                a.uuid AS id,
                c.uuid AS contact_id,
                c.email AS contact_email,
                a.created_at,
                a.answers
            FROM website_forms_answers a
             LEFT JOIN website_forms f ON a.form_id = f.id
             LEFT JOIN community_contacts c ON a.contact_id = c.id
            WHERE a.form_id = ?
            ORDER BY f.created_at DESC
        ');

        foreach ($query->executeQuery([$form->getId()])->iterateAssociative() as $i => $record) {
            $exported = $record;
            unset($exported['answers']);

            $answers = Json::decode($record['answers']);
            foreach ($answers as $key => $value) {
                $exported[$key] = $value;
            }

            yield $i => $exported;
        }
    }

    /**
     * @return FormAnswer[]|iterable
     */
    public function findContactHistory(Contact $contact): iterable
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'f')
            ->leftJoin('a.form', 'f')
            ->where('a.contact = :contact')
            ->setParameter('contact', $contact->getId())
            ->setMaxResults(50)
            ->getQuery()
            ->toIterable()
        ;
    }
}
