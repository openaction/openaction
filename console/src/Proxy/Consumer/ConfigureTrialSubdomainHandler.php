<?php

namespace App\Proxy\Consumer;

use App\Bridge\Cloudflare\CloudflareInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConfigureTrialSubdomainHandler
{
    private CloudflareInterface $cloudflare;

    public function __construct(CloudflareInterface $cf)
    {
        $this->cloudflare = $cf;
    }

    public function __invoke(ConfigureTrialSubdomainMessage $message)
    {
        // If the Cloudflare token is not configured, ignore messages
        if (!$this->cloudflare->isEnabled()) {
            return;
        }

        if (!$this->cloudflare->hasTrialSubdomain($message->getSubdomain())) {
            $this->cloudflare->createTrialSubdomain($message->getSubdomain());
        }
    }
}
