<?php

namespace App\Command\Community;

use App\Bridge\Postmark\Consumer\PostmarkMessage;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Repository\Community\EmailBatchRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:community:dispatch-email-batches',
    description: 'Dispatch due email batches to the async emailing transport.',
)]
class DispatchEmailBatchesCommand extends Command
{
    public function __construct(
        private readonly EmailBatchRepository $repository,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Maximum number of batches to dispatch in a single run', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = max(1, (int) $input->getOption('limit'));
        $now = new \DateTime();
        $dispatched = 0;

        do {
            $batches = $this->repository->findDueCampaignBatches($now, $limit);

            foreach ($batches as $batch) {
                if (!$this->repository->markQueued($batch, $now)) {
                    continue;
                }

                if ('postmark' === $batch->getEmailProvider()) {
                    $this->bus->dispatch(new PostmarkMessage($batch->getId()));
                } else {
                    $this->bus->dispatch(new SendgridMessage($batch->getId()));
                }

                ++$dispatched;
            }
        } while ($batches && count($batches) === $limit);

        $io->success(sprintf('Dispatched %d email batch(es).', $dispatched));

        return Command::SUCCESS;
    }
}
