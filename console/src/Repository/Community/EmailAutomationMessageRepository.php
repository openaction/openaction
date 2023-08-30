<?php

namespace App\Repository\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\EmailAutomationMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailAutomationMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailAutomationMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailAutomationMessage[]    findAll()
 * @method EmailAutomationMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailAutomationMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailAutomationMessage::class);
    }

    public function createMessage(EmailAutomation $automation, Contact $contact)
    {
        $this->_em->persist(EmailAutomationMessage::createFromContact($automation, $contact));
        $this->_em->flush();
    }

    public function createNotificationMessage(EmailAutomation $automation)
    {
        $this->_em->persist(new EmailAutomationMessage($automation, $automation->getToEmail()));
        $this->_em->flush();
    }
}
