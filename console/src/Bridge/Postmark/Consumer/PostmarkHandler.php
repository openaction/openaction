<?php

namespace App\Bridge\Postmark\Consumer;

use App\Bridge\Postmark\PostmarkInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PostmarkHandler
{
    private PostmarkInterface $postmark;
    private LoggerInterface $logger;

    public function __construct(PostmarkInterface $postmark, LoggerInterface $logger)
    {
        $this->postmark = $postmark;
        $this->logger = $logger;
    }

    public function __invoke(PostmarkMessage $message)
    {
        $this->logger->info('Sending email '.$message->getMail()->subject, [
            'mail' => $message->getMail(),
        ]);

        $this->postmark->sendMessage($message->getMail());
    }
}
