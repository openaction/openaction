<?php

namespace App\Bridge\Sendgrid\Consumer;

use App\Bridge\Sendgrid\SendgridInterface;
use App\Community\SendgridMailFactory;
use App\Repository\Community\EmailBatchRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendgridHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EmailBatchRepository $repository,
        private readonly SendgridMailFactory $mailFactory,
        private readonly SendgridInterface $sendgrid,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendgridMessage $message)
    {
        if (!$batch = $this->repository->find($message->batchId)) {
            $this->logger->error(sprintf('Batch %d not found', $message->batchId));

            return;
        }

        if ($batch->getSentAt()) {
            $this->logger->info('Batch already sent at '.$batch->getSentAt()->format('Y-m-d H:i:s').', skipping');

            return;
        }

        $mail = $this->mailFactory->createMailFromBatch($batch);

        $this->logger->info('Sending email '.$mail->getGlobalSubject()->getSubject());
        $this->sendgrid->sendMessage($mail);

        $this->repository->markSent($batch);
    }
}
