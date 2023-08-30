<?php

namespace App\Bridge\Spallian\Consumer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SpallianHandler implements MessageHandlerInterface
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function __invoke(SpallianMessage $message)
    {
        $this->logger->info('Synchronizing contact with Spallian ', $message->getPayload());

        $this->httpClient
            ->request('POST', $message->getEndpoint(), ['json' => $message->getPayload()])
            ->getContent()
        ;
    }
}
