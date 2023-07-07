<?php

namespace App\Repository\Integration;

use App\Entity\Integration\IntegromatWebhook;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IntegromatWebhook|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntegromatWebhook|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntegromatWebhook[]    findAll()
 * @method IntegromatWebhook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntegromatWebhookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegromatWebhook::class);
    }

    public function attachWebhook(Organization $organization, string $url): IntegromatWebhook
    {
        $webhook = new IntegromatWebhook($organization, $url);

        $this->_em->persist($webhook);
        $this->_em->flush();

        return $webhook;
    }

    public function detachWebhook(Organization $organization, string $token)
    {
        if ($webhook = $this->findOneBy(['organization' => $organization, 'token' => $token])) {
            $this->_em->remove($webhook);
            $this->_em->flush();
        }
    }

    public function removeWebhook(int $id)
    {
        if ($webhook = $this->find($id)) {
            $this->_em->remove($webhook);
            $this->_em->flush();
        }
    }
}
