<?php

namespace App\Bridge\Revue\Consumer;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Api\Model\ContactApiData;
use App\Api\Persister\ContactApiPersister;
use App\Bridge\Revue\RevueInterface;
use App\Entity\Integration\RevueAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class RevueSyncHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private RevueInterface $revue;
    private ContactApiPersister $contactPersister;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, RevueInterface $r, ContactApiPersister $p, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->revue = $r;
        $this->contactPersister = $p;
        $this->bus = $bus;
    }

    public function __invoke(RevueSyncMessage $message)
    {
        /** @var RevueAccount $account */
        if (!$account = $this->em->find(RevueAccount::class, $message->getAccountId())) {
            // Account doesn't exist anymore
            return true;
        }

        if (!$account->isEnabled()) {
            // Account is failing
            return true;
        }

        try {
            // Fetch Revue subscribers
            $subscribers = $this->revue->getSubscribers($account->getApiToken());
        } catch (\InvalidArgumentException) {
            // If an auth error occurs fetching the subscribers, disable the account to display an error message
            $account->disable();

            $this->em->persist($account);
            $this->em->flush();

            return true;
        }

        // Only add subscribers which were updated since the last sync
        $toPersist = [];
        foreach ($subscribers as $subscriber) {
            if (empty($subscriber['email'])) {
                continue;
            }

            if (!$account->getLastSync() || $account->getLastSync() < new \DateTime($subscriber['last_changed'])) {
                $toPersist[] = $this->createApiDataFromSubscriber($subscriber);
            }
        }

        // Persist contacts
        foreach ($toPersist as $data) {
            $this->contactPersister->persist($data, $account->getOrganization());
        }

        // Mark account as synced now
        $account->markSynced();

        $this->em->persist($account);
        $this->em->flush();

        return true;
    }

    private function createApiDataFromSubscriber(array $subscriber): ContactApiData
    {
        $data = new ContactApiData();
        $data->email = $subscriber['email'];
        $data->profileFirstName = $subscriber['first_name'];
        $data->profileLastName = $subscriber['last_name'];
        $data->metadataTags = ['TwitterRevue'];
        $data->metadataSource = 'revue:'.$subscriber['id'];

        return $data;
    }
}
