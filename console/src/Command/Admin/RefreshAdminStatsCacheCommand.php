<?php

namespace App\Command\Admin;

use App\Admin\DashboardStatsResolver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:admin:refresh-stats',
    description: 'Refresh admin stats cache.',
)]
class RefreshAdminStatsCacheCommand extends Command
{
    public function __construct(
        private readonly DashboardStatsResolver $dashboardStatsResolver,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Refreshing index stats');
        $this->dashboardStatsResolver->refreshAdminIndexStats();

        $io->text('Refreshing billing stats');
        $this->dashboardStatsResolver->refreshAdminBillingStats();

        $io->success('Stats refreshed.');

        return Command::SUCCESS;
    }
}
