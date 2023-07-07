<?php

namespace App\Command\Community;

use App\Entity\Community\Contact;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(
    name: 'app:community:export',
    description: 'Export contacts of a given organization.',
)]
class ExportContactsCommand extends Command
{
    private EntityManagerInterface $em;
    private string $projectDir;

    public function __construct(EntityManagerInterface $em, string $projectDir)
    {
        parent::__construct();

        $this->em = $em;
        $this->projectDir = $projectDir;
    }

    protected function configure()
    {
        $this->addArgument('organization-uuid', InputArgument::REQUIRED, 'Organization UUID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$orga = $this->em->getRepository(Organization::class)->findOneByUuid($input->getArgument('organization-uuid'))) {
            throw new \InvalidArgumentException('Organization not found');
        }

        $filename = date('Y-m-d').'-'.(new AsciiSlugger())->slug($orga->getName())->lower().'-contacts.xlsx';

        $io = new SymfonyStyle($input, $output);
        $io->text('Exporting to var/'.$filename.' ...');

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($this->projectDir.'/var/'.$filename);

        $headerAdded = false;
        foreach ($this->em->getRepository(Contact::class)->getExportData($orga) as $contact) {
            if (!$headerAdded) {
                $writer->addRow(WriterEntityFactory::createRowFromArray(array_keys($contact)));
                $headerAdded = true;
            }

            $writer->addRow(WriterEntityFactory::createRowFromArray($contact));
        }

        $writer->close();

        $io->success('Exported.');

        return Command::SUCCESS;
    }
}
