<?php

namespace App\Command\Community;

use App\Community\Ambiguity\ContactAmbiguitiesResolver;
use App\Repository\OrganizationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:community:resolve-ambiguities',
    description: 'Resolve contacts ambiguities.',
)]
class ResolveContactAmbiguitiesCommand extends Command
{
    private OrganizationRepository $organizationRepository;
    private ContactAmbiguitiesResolver $resolver;

    public function __construct(OrganizationRepository $organizationRepository, ContactAmbiguitiesResolver $resolver)
    {
        parent::__construct();

        $this->organizationRepository = $organizationRepository;
        $this->resolver = $resolver;
    }

    protected function configure()
    {
        $this
            ->addOption('organization-uuid', 'o', InputOption::VALUE_REQUIRED, 'Limit computing to the given organization.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist anything.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orga = null;
        if ($orgaUuid = $input->getOption('organization-uuid')) {
            $orga = $this->organizationRepository->findOneBy(['uuid' => $orgaUuid]);

            if (!$orga) {
                throw new \InvalidArgumentException('Organization not found');
            }
        }

        $io = new SymfonyStyle($input, $output);

        $io->text('Resolving...');
        $ambiguities = $this->resolver->resolveAmbiguities($orga);
        $io->success('Resolving succeeded, '.count($ambiguities).' ambiguities found.');

        if (!$input->getOption('dry-run')) {
            $io->text('Persisting...');
            $this->resolver->persistResolvedAmbiguities($ambiguities, $orga);
            $io->success('Persisting succeeded, '.count($ambiguities).' ambiguities persisted.');
        }

        return Command::SUCCESS;
    }
}
