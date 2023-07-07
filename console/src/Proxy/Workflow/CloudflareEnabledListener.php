<?php

namespace App\Proxy\Workflow;

use App\Bridge\Cloudflare\CloudflareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class CloudflareEnabledListener implements EventSubscriberInterface
{
    private CloudflareInterface $cloudflare;

    public function __construct(CloudflareInterface $cloudflare)
    {
        $this->cloudflare = $cloudflare;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.domain_configuration.guard.cloudflare_create' => ['guardCloudflareEnabled'],
            'workflow.domain_configuration.guard.cloudflare_provision' => ['guardCloudflareEnabled'],
            'workflow.domain_configuration.guard.cloudflare_configure' => ['guardCloudflareEnabled'],
            'workflow.domain_configuration.guard.sendgrid_configure' => ['guardCloudflareEnabled'],
            'workflow.domain_configuration.guard.postmark_configure' => ['guardCloudflareEnabled'],
        ];
    }

    public function guardCloudflareEnabled(GuardEvent $event)
    {
        if (!$this->cloudflare->isEnabled()) {
            $event->setBlocked(true, 'This domain cannot be configured because the Cloudflare integration is disabled.');
        }
    }
}
