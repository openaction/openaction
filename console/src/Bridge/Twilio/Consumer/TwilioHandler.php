<?php

namespace App\Bridge\Twilio\Consumer;

use App\Bridge\Twilio\Model\Personalization;
use App\Bridge\Twilio\TwilioInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TwilioHandler
{
    private TwilioInterface $twilio;
    private LoggerInterface $logger;

    public function __construct(TwilioInterface $twilio, LoggerInterface $logger)
    {
        $this->twilio = $twilio;
        $this->logger = $logger;
    }

    public function __invoke(TwilioMessage $message)
    {
        foreach ($message->getPersonalizations() as $personalization) {
            $body = $this->createPersonalizedBody($message->getBody(), $personalization);

            $this->logger->debug('Sending Twilio text message.', ['to' => $personalization->getTo(), 'body' => $body]);

            try {
                $this->twilio->sendMessage(
                    $message->getFrom(),
                    $personalization->getTo(),
                    $body,
                    $personalization->getStatusCallbackUrl()
                );
            } catch (\Exception $e) {
                // Ignore failures: this is most likely because the user unsubscribed, still log but don't stop
                $this->logger->error('Sending Twilio text message failed.', ['exception' => $e]);
            }

            // Sleep 100ms to avoid reaching API rate limit
            usleep(100_000);
        }
    }

    private function createPersonalizedBody(string $body, Personalization $personalization): string
    {
        return str_replace(
            array_keys($personalization->getVariables()),
            array_values($personalization->getVariables()),
            $body
        );
    }
}
