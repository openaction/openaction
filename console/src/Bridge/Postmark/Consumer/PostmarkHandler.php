<?php

namespace App\Bridge\Postmark\Consumer;

use App\Bridge\Postmark\PostmarkInterface;
use App\Community\PostmarkMailFactory;
use App\Repository\Community\EmailingCampaignBatchRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PostmarkHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EmailingCampaignBatchRepository $repository,
        private readonly PostmarkMailFactory $mailFactory,
        private readonly PostmarkInterface $postmark,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(PostmarkMessage $message)
    {
        if (!$batch = $this->repository->find($message->batchId)) {
            $this->logger->error(sprintf('Batch %d not found', $message->batchId));

            return;
        }

        $mail = $this->mailFactory->createMailFromBatch($batch);

        $this->logger->info('Sending email '.$mail->subject);
        $this->postmark->sendMessage($mail);
    }
}
