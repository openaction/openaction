<?php

namespace App\Search\Consumer;

use App\Search\CrmIndexer;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateCrmDocumentHandler
{
    public function __construct(private CrmIndexer $crmIndexer)
    {
    }

    public function __invoke(UpdateCrmDocumentsMessage $message, ?Acknowledger $ack = null)
    {
        $this->crmIndexer->synchronizeContacts(
            $message->getOrganizationUuid(),
            $message->getIndexVersion(),
            array_keys($message->getContactsIdentifiers()),
        );
    }
}
