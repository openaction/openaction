<?php

namespace App\Proxy\Consumer;

use App\Bridge\Cloudflare\CloudflareInterface;
use App\Bridge\Postmark\PostmarkInterface;
use App\Bridge\Sendgrid\SendgridInterface;
use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;

trait DomainHandlerTrait
{
    private WorkflowInterface $workflow;
    private EntityManagerInterface $manager;
    private MessageBusInterface $bus;
    private CloudflareInterface $cloudflare;
    private SendgridInterface $sendgrid;
    private PostmarkInterface $postmark;
    private LoggerInterface $logger;

    public function __construct(
        WorkflowInterface $domainConfigurationWorkflow,
        EntityManagerInterface $manager,
        MessageBusInterface $bus,
        CloudflareInterface $cloudflare,
        SendgridInterface $sendgrid,
        PostmarkInterface $postmark,
        LoggerInterface $logger
    ) {
        $this->workflow = $domainConfigurationWorkflow;
        $this->manager = $manager;
        $this->bus = $bus;
        $this->cloudflare = $cloudflare;
        $this->sendgrid = $sendgrid;
        $this->postmark = $postmark;
        $this->logger = $logger;
    }

    private function findDomain(int $id): ?Domain
    {
        if (!$domain = $this->manager->find(Domain::class, $id)) {
            $this->logger->error('Domain not found for ID.', ['domain_id' => $id]);

            return null;
        }

        if (!$domain->isManagedAutomatically()) {
            return null;
        }

        return $domain;
    }
}
