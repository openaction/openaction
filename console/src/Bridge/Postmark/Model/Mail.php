<?php

namespace App\Bridge\Postmark\Model;

class Mail
{
    /**
     * @param Personalization[] $personalizations
     */
    public function __construct(
        public readonly string $fromEmail,
        public readonly string $fromName,
        public readonly ?string $replyToEmail,
        public readonly ?string $replyToName,
        public readonly string $subject,
        public readonly bool $trackOpens,
        public readonly bool $trackClicks,
        public readonly array $personalizations,
    ) {
    }
}
