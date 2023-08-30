<?php

namespace App\Community;

use App\Bridge\Twilio\Consumer\TwilioMessage;
use App\Bridge\Twilio\Model\Personalization;
use App\Bridge\Twilio\Model\Recipient;
use App\Entity\Community\TextingCampaign;
use App\Util\PhoneNumber;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TextingMessageFactory
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function createMessage(TextingCampaign $campaign, array $recipients): TwilioMessage
    {
        $personalizations = [];

        /** @var Recipient $recipient */
        foreach ($recipients as $recipient) {
            $personalizations[] = new Personalization(
                PhoneNumber::formatDatabase($recipient->getNumber()),
                $recipient->getVariables(),
                $this->router->generate(
                    'webhook_twilio',
                    ['messageId' => $recipient->getMessageId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            );
        }

        return new TwilioMessage(
            $campaign->getProject()->getOrganization()->getTextingSenderCode(),
            $campaign->getContent(),
            $personalizations
        );
    }
}
