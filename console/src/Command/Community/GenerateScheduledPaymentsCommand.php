<?php

namespace App\Command\Community;

use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactSubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:community:generate-scheduled-payments',
    description: 'Generate the next pending payment for due active subscriptions.',
)]
class GenerateScheduledPaymentsCommand extends Command
{
    public function __construct(
        private readonly ContactSubscriptionRepository $subscriptions,
        private readonly ContactPaymentRepository $payments,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Maximum number of subscriptions to inspect in one run', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $today = new \DateTimeImmutable('today');
        $limit = max(1, (int) $input->getOption('limit'));

        $generated = 0;
        $skipped = 0;

        foreach ($this->subscriptions->findActiveNonExpired($today, $limit) as $subscription) {
            $latestPayment = $this->payments->findLatestForSubscription($subscription);
            if (!$latestPayment) {
                ++$skipped;

                continue;
            }

            $latestDate = \DateTimeImmutable::createFromMutable($latestPayment->getCreatedAt())->setTime(0, 0, 0);
            if ($latestDate > $today) {
                ++$skipped;

                continue;
            }

            $nextDate = $latestDate->modify(sprintf('+%d months', $subscription->getIntervalInMonths()));

            if ($subscription->getEndsAt() && $nextDate > $subscription->getEndsAt()) {
                ++$skipped;

                continue;
            }

            if ($this->payments->existsForSubscriptionAndDate($subscription, $nextDate)) {
                ++$skipped;

                continue;
            }

            $payment = $subscription->createPaymentForDate($nextDate);
            $this->em->persist($payment);
            ++$generated;
        }

        if ($generated > 0) {
            $this->em->flush();
        }

        $io->success(sprintf('Generated %d scheduled payment(s), skipped %d subscription(s).', $generated, $skipped));

        return Command::SUCCESS;
    }
}
