<?php

namespace App\Bridge\Sendgrid\Consumer;

use App\Bridge\Sendgrid\SendgridInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendgridHandler
{
    private SendgridInterface $sendgrid;
    private LoggerInterface $logger;

    public function __construct(SendgridInterface $sendgrid, LoggerInterface $logger)
    {
        $this->sendgrid = $sendgrid;
        $this->logger = $logger;
    }

    public function __invoke(SendgridMessage $message)
    {
        $this->logger->info('Sending email '.$message->getMail()->getGlobalSubject()->getSubject(), [
            'mail' => $message->getMail(),
        ]);

        $this->sendgrid->sendMessage($message->getMail());
    }
}
