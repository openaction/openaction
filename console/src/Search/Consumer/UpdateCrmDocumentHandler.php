<?php

namespace App\Search\Consumer;

use App\Search\CrmIndexer;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateCrmDocumentHandler implements MessageHandlerInterface
{
    public function __construct(private CrmIndexer $crmIndexer)
    {
    }

    public function __invoke(UpdateCrmDocumentsMessage $message, Acknowledger $ack = null)
    {
        $this->crmIndexer->updateDocuments(
            $message->getOrganizationUuid(),
            $message->getIndexVersion(),
            $message->getContactsIdentifiers(),
        );
    }
}
