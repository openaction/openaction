<?php

namespace App\Command\Tools;

use App\Proxy\DomainRouter;
use App\Repository\Community\PhoningCampaignRepository;
use App\Repository\ProjectRepository;
use App\Util\Uid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

#[AsCommand(
    name: 'app:tools:list-phoning-campaigns',
    description: 'List the phoning campaigns of a given project with their URL to be exported as Excel.',
)]
class ListPhoningCampaignsCommand extends Command
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private PhoningCampaignRepository $phoningCampaignRepository,
        private DomainRouter $domainRouter,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('project-uuid', InputArgument::REQUIRED, 'The project UUID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $this->projectRepository->findOneBy(['uuid' => $input->getArgument('project-uuid')]);
        if (!$project) {
            throw new \InvalidArgumentException('Project '.$input->getArgument('project-uuid').' not found.');
        }

        $campaigns = $this->phoningCampaignRepository->findBy(['project' => $project], ['name' => 'ASC']);

        $data = [];
        foreach ($campaigns as $campaign) {
            $data[] = [
                'name' => $campaign->getName(),
                'url' => $this->domainRouter->generateRedirectUrl($project, 'phoning', Uid::toBase62($campaign->getUuid())),
                'is_active' => $campaign->isActive(),
                'is_finished' => $campaign->isFinished(),
            ];
        }

        $output->writeln((new CsvEncoder())->encode($data, 'csv'));

        return Command::SUCCESS;
    }
}
