<?php

namespace App\Search\Consumer;

use App\Search\CmsIndexer;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RemoveCmsDocumentHandler
{
    public function __construct(
        private readonly CmsIndexer $cmsIndexer,
    ) {
    }

    public function __invoke(RemoveCmsDocumentMessage $message, ?Acknowledger $ack = null)
    {
        $this->cmsIndexer->unindexDocument($message->getDocumentId());
    }
}
