<?php

namespace App\Command\Proxy;

use App\Proxy\DomainTokenCache;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:proxy:refresh-domains-cache',
    description: 'Refresh the domains tokens cache used by public to resolve which token to use for a given domain.',
)]
class RefreshDomainsCacheCommand extends Command
{
    private DomainTokenCache $cache;

    public function __construct(DomainTokenCache $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cache->refresh();

        $io = new SymfonyStyle($input, $output);
        $io->success('Cache refreshed.');

        return Command::SUCCESS;
    }
}
