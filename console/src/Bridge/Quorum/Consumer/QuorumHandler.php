<?php

namespace App\Bridge\Quorum\Consumer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class QuorumHandler
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function __invoke(QuorumMessage $message)
    {
        $this->logger->info('Synchronizing contact with Quorum ', $message->getPayload());

        $this->httpClient
            ->request('POST', 'https://production.quorumapps.com/nationbuilder/webhooksync', [
                'json' => $message->getPayload(),
            ])
            ->getContent()
        ;
    }
}
