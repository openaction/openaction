<?php

namespace App\Repository\Community;

use App\Api\Model\ContactUpdateEmailApiData;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactUpdate;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactUpdate|null findOneByBase62Uid(string $base62Uid)
 * @method ContactUpdate[]    findAll()
 * @method ContactUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactUpdateRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactUpdate::class);
    }

    public function createContactEmailUpdate(ContactUpdateEmailApiData $contactUpdateApiData): ContactUpdate
    {
        return $this->_em->wrapInTransaction(function () use ($contactUpdateApiData) {
            $contact = $contactUpdateApiData->getContact();

            $this->deleteByContact($contact, ContactUpdate::TYPE_EMAIL);

            $contactUpdate = ContactUpdate::createEmailUpdate($contact, $contactUpdateApiData->newEmail);
            $this->_em->persist($contactUpdate);
            $this->_em->flush();

            return $contactUpdate;
        });
    }

    public function createContactUnregisterUpdate(Contact $contact): ContactUpdate
    {
        return $this->_em->wrapInTransaction(function () use ($contact) {
            $this->deleteByContact($contact, ContactUpdate::TYPE_UNREGISTER);

            $contactUpdate = ContactUpdate::createUnregister($contact);

            $this->_em->persist($contactUpdate);
            $this->_em->flush();

            return $contactUpdate;
        });
    }

    public function deleteByContact(Contact $contact, string $type)
    {
        $this->_em->createQueryBuilder()
            ->delete(ContactUpdate::class, 'c')
            ->where('c.contact = :contact')
            ->setParameter('contact', $contact)
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->execute()
        ;
    }

    public function apply(ContactUpdate $contactUpdate)
    {
        $contact = $contactUpdate->getContact();

        $this->_em->remove($contactUpdate);
        $this->_em->persist($contact);
        $this->_em->flush();

        return $contact;
    }
}
