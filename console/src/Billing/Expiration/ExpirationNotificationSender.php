<?php

namespace App\Billing\Expiration;

use App\Entity\Model\SubscriptionNotifications;
use App\Mailer\PlatformMailer;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExpirationNotificationSender
{
    private OrganizationRepository $repository;
    private ExpirationNotificationResolver $resolver;
    private EntityManagerInterface $em;
    private PlatformMailer $mailer;

    public function __construct(OrganizationRepository $or, ExpirationNotificationResolver $r, EntityManagerInterface $em, PlatformMailer $m)
    {
        $this->repository = $or;
        $this->resolver = $r;
        $this->em = $em;
        $this->mailer = $m;
    }

    /**
     * Send expiration notifications and return the list of notified organization names.
     *
     * @return string[]
     */
    public function sendSubscriptionExpirationNotifications(bool $dryRun = false): array
    {
        $organizations = $this->repository->findActiveSubscriptions();
        $sent = [];

        foreach ($organizations as $orga) {
            $notifications = $orga->getSubscriptionNotifications();

            // Check if a notification should be sent
            $shouldNotify = $this->resolver->shouldSendNotification(
                $orga->getSubscriptionCurrentPeriodEnd(),
                $notifications->getDatesFor(SubscriptionNotifications::TYPE_EXPIRATION),
            );

            if (!$shouldNotify) {
                continue;
            }

            $orga->setSubscriptionNotifications(
                $notifications->withMarkedNotified(SubscriptionNotifications::TYPE_EXPIRATION, new \DateTime())
            );

            if (!$dryRun) {
                $this->em->persist($orga);
                $this->em->flush();

                // Send to all admins
                foreach ($orga->getAdmins() as $admin) {
                    $this->mailer->sendNotificationSubscriptionExpiration($admin, $orga);
                }
            }

            $sent[] = $orga->getName();
        }

        return $sent;
    }
}
