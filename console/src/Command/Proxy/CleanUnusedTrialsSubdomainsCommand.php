<?php

namespace App\Command\Proxy;

use App\Bridge\Cloudflare\CloudflareInterface;
use App\Proxy\Consumer\CloudflareCheckDomainMessage;
use App\Proxy\Consumer\CloudflareProvisionDomainMessage;
use App\Repository\DomainRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:proxy:clean-unused-trials-subdomains',
    description: 'Remove unused subdomains from trial domain on Cloudflare.',
)]
class CleanUnusedTrialsSubdomainsCommand extends Command
{
    public function __construct(
        private readonly DomainRepository $domainRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly CloudflareInterface $cloudflare,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Findind all Cloudflare subdomains for trial domain...');
        $cloudflareSubdomains = $this->cloudflare->getAllTrialSubdomains();
        $cloudflareRecordsIds = array_flip($cloudflareSubdomains);

        $io->text('Fetch all trial projects subdomains...');
        $trialDomain = $this->domainRepository->getTrialDomain();
        $trialProjectsSubdomains = $this->projectRepository->findSubdomainsUsedForDomain($trialDomain);

        $toRemove = array_diff($cloudflareSubdomains, $trialProjectsSubdomains);
        $io->text('Found '.count($toRemove).' subdomains to remove as they are not used anymore');

        $io->text('Removing...');

        $progress = new ProgressBar($io);
        foreach ($progress->iterate($toRemove) as $subdomain) {
            $this->cloudflare->removeTrialSubdomain($cloudflareRecordsIds[$subdomain]);
        }
        $progress->finish();

        $io->success('Cleaned');

        return Command::SUCCESS;
    }
}
