<?php

namespace App\Command\Tools;

use App\Community\Consumer\StartPhoningCampaignMessage;
use App\DataManager\PhoningCampaignDataManager;
use App\Entity\Area;
use App\Form\Community\Model\PhoningCampaignMetaData;
use App\Repository\AreaRepository;
use App\Repository\Community\PhoningCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:tools:create-phoning-campaigns-by-area',
    description: 'Create a series of phoning campaigns by area from a source one.',
)]
class CreatePhoningCampaignsByAreaCommand extends Command
{
    public function __construct(
        private PhoningCampaignRepository $phoningCampaignRepository,
        private AreaRepository $areaRepository,
        private PhoningCampaignDataManager $dataManager,
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('source-uuid', InputArgument::REQUIRED, 'The phoning campaign to duplicate')
            ->addArgument('area-type', InputArgument::REQUIRED, 'The type of area to create campaigns for')
            ->addArgument('country', InputArgument::REQUIRED, 'The country in which to create the campaigns')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist anything')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching source campaign ...');

        $campaign = $this->phoningCampaignRepository->findOneBy(['uuid' => $input->getArgument('source-uuid')]);
        if (!$campaign) {
            throw new \InvalidArgumentException('Campaign '.$input->getArgument('source-uuid').' not found.');
        }

        $country = $this->areaRepository->findOneBy(['type' => Area::TYPE_COUNTRY, 'code' => $input->getArgument('country')]);
        if (!$country) {
            throw new \InvalidArgumentException('Country '.$input->getArgument('country').' not found.');
        }

        $areas = $this->areaRepository->findChildrenByType($country, $input->getArgument('area-type'));
        if (!$areas) {
            throw new \InvalidArgumentException('No areas of type '.$input->getArgument('area-type').' found in country.');
        }

        $output->writeln('');
        $output->writeln('Source campaign: '.$campaign->getName());
        $output->writeln(count($areas).' areas targeted: '.implode(', ', array_slice(array_column($areas, 'name'), 0, 5)));
        $output->writeln('');

        $question = new ConfirmationQuestion('Create the campaigns? [Y/n] ', true);
        if (!$this->getHelper('question')->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $output->writeln('');
        $output->writeln('Creating...');

        foreach ($areas as $area) {
            if ($input->getOption('dry-run')) {
                continue;
            }

            $output->writeln('  - '.$area['name']);
            $output->writeln('    Duplicating and applying filters ...');

            $duplicate = $this->dataManager->duplicate($campaign);

            $metadata = PhoningCampaignMetaData::createFromCampaign($duplicate);
            $metadata->name = $duplicate->getName().' - '.$area['name'];
            $metadata->tagsFilter = null;
            $metadata->contactsFilter = null;
            $metadata->onlyForMembers = false;
            $metadata->areasFilterIds = json_encode([$area['id'] => $area['name']]);

            $duplicate->applyMetadataUpdate($metadata);
            $duplicate->start();

            $this->em->persist($duplicate);
            $this->em->flush();

            $this->phoningCampaignRepository->updateFilters($duplicate, $metadata);

            $output->writeln('    Starting campaign ...');
            $this->bus->dispatch(new StartPhoningCampaignMessage($duplicate->getId()));

            $output->writeln('    Done');
        }

        return Command::SUCCESS;
    }
}
