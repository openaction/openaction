<?php

namespace App\Command\Community;

use App\Community\ImportExport\ContactImporter;
use App\Form\Community\Model\ImportMetadataData;
use App\Repository\OrganizationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

#[AsCommand(name: 'app:community:import')]
class ImportContactsCommand extends Command
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly ContactImporter $importer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching organization...');
        $orga = $this->organizationRepository->findOneBy(['name' => 'Citipo']);

        $output->writeln('Preparing import...');
        $import = $this->importer->prepareImport($orga, new File(__DIR__.'/../../../var/data.xlsx'));

        $metadata = ImportMetadataData::createFromImport($import);
        $metadata->areaId = 39389989938296926;
        $metadata->columnsTypes = [
            0 => 'profileFirstName',
            1 => 'ignored',
            2 => 'profileLastName',
            3 => 'ignored',
            4 => 'ignored',
            5 => 'ignored',
            6 => 'email',
            7 => 'ignored',
            8 => 'ignored',
            9 => 'ignored',
            10 => 'ignored',
            11 => 'ignored',
            12 => 'ignored',
            13 => 'ignored',
            14 => 'ignored',
            15 => 'ignored',
            16 => 'ignored',
            17 => 'contactPhone',
            18 => 'contactWorkPhone',
            19 => 'ignored',
            20 => 'addressStreetLine1',
            21 => 'addressStreetLine2',
            22 => 'ignored',
            23 => 'addressCity',
            24 => 'ignored',
            25 => 'ignored',
            26 => 'addressZipCode',
            27 => 'addressCountry',
            28 => 'ignored',
            29 => 'metadataTagsList',
        ];

        $output->writeln('Importing...');
        $this->importer->startImport($import, $metadata);

        return Command::SUCCESS;
    }
}
