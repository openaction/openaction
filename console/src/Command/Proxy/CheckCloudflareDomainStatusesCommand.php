<?php

namespace App\Command\Proxy;

use App\Proxy\Consumer\CloudflareCheckDomainMessage;
use App\Proxy\Consumer\CloudflareProvisionDomainMessage;
use App\Repository\DomainRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:proxy:check-cloudflare',
    description: 'Dispatch domains statuses check messages for Cloudflare.',
)]
class CheckCloudflareDomainStatusesCommand extends Command
{
    private DomainRepository $repository;
    private MessageBusInterface $bus;

    public function __construct(DomainRepository $dr, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->repository = $dr;
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->repository->findDomainsWithoutStatus('cloudflare_ready') as $domain) {
            if ($domain->getConfigurationStatus()['cloudflare_created'] ?? false) {
                $this->bus->dispatch(new CloudflareProvisionDomainMessage($domain->getId()));
            } else {
                $this->bus->dispatch(new CloudflareCheckDomainMessage($domain->getId()));
            }
        }

        $io->success('Messages dispatched.');

        return Command::SUCCESS;
    }
}
