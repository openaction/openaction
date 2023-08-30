<?php

namespace App\Bridge\Quorum\Consumer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class QuorumHandler implements MessageHandlerInterface
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
