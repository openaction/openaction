<?php

namespace App\Command\Subscription;

use App\Billing\Expiration\ExpirationNotificationSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:subscription:send-expiration-notifications',
    description: 'Send expiration notifications for soon to expire subscriptions.',
)]
class SendExpirationNotificationsCommand extends Command
{
    private ExpirationNotificationSender $sender;

    public function __construct(ExpirationNotificationSender $sender)
    {
        parent::__construct();

        $this->sender = $sender;
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist and send anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Sending notifications...');

        $sent = $this->sender->sendSubscriptionExpirationNotifications($input->getOption('dry-run'));
        foreach ($sent as $name) {
            $io->text('Notified '.$name);
        }

        $io->success(count($sent).' notifications sent.');

        return Command::SUCCESS;
    }
}
