<?php

namespace App\Bridge\Integromat\Consumer;

use App\Repository\Integration\IntegromatWebhookRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class IntegromatWebhookHandler
{
    private HttpClientInterface $httpClient;
    private IntegromatWebhookRepository $repository;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, IntegromatWebhookRepository $repository, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function __invoke(IntegromatWebhookMessage $message)
    {
        $this->logger->info('Triggering Integromat webhook', [
            'id' => $message->getWebhookId(),
            'url' => $message->getUrl(),
            'payload' => $message->getPayload(),
        ]);

        $response = $this->httpClient->request('POST', $message->getUrl(), ['json' => $message->getPayload()]);

        if (410 === $response->getStatusCode()) {
            $this->logger->info('Integromat webhook has been removed, deleting in database', [
                'id' => $message->getWebhookId(),
                'url' => $message->getUrl(),
                'payload' => $message->getPayload(),
            ]);

            // Webhook has been removed, remove in database
            $this->repository->removeWebhook($message->getWebhookId());

            return;
        }

        // Trigger other errors for Sentry to catch
        $response->getContent();
    }
}
