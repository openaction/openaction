<?php

namespace App\Tests\Search\Consumer;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\RemoveCrmDocumentHandler;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Tests\KernelTestCase;

class RemoveCrmDocumentHandlerTest extends KernelTestCase
{
    public function testConsumeValid()
    {
        self::bootKernel();

        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'tchalut@yahoo.fr']);

        // Apply handler
        $handler = static::getContainer()->get(RemoveCrmDocumentHandler::class);
        $handler(new RemoveCrmDocumentMessage($contact->getOrganization()->getId(), $contact->getUuid()->toRfc4122()));

        // Check Meilisearch is updated
        $documents = static::getContainer()->get(MeilisearchInterface::class)->search(
            $contact->getOrganization()->getCrmIndexName(),
            'tchalut@yahoo.fr'
        );

        $this->assertEmpty($documents['hits']);
    }
}
