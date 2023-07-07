<?php

namespace App\Search\Consumer;

use App\Search\CmsIndexer;
use App\Search\Model\Searchable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateCmsDocumentHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CmsIndexer $cmsIndexer,
    ) {
    }

    public function __invoke(UpdateCmsDocumentMessage $message, Acknowledger $ack = null)
    {
        /** @var Searchable|null $entity */
        $entity = $this->em->getRepository($message->getEntityClass())->find($message->getEntityId());
        if (!$entity) {
            return;
        }

        if ($entity->isSearchPublic()) {
            $this->cmsIndexer->indexDocument($entity);
        }
    }
}
