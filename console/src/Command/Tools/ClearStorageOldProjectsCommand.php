<?php

namespace App\Command\Tools;

use App\Repository\ProjectRepository;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tools:clear-storage-old-projects',
    description: 'Remove storage directories associated to projects which do not exist anymore.',
)]
class ClearStorageOldProjectsCommand extends Command
{
    private ProjectRepository $repository;
    private FilesystemOperator $storage;

    public function __construct(ProjectRepository $repository, FilesystemOperator $cdnStorage)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->storage = $cdnStorage;
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not remove anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Listing storage directories
        $output->writeln('Listing directories ...');

        /** @var StorageAttributes[] $directories */
        $directories = $this->storage->listContents('.');

        // Clearing removed projects storage
        $output->writeln('Clearing storage of removed projects ...');
        foreach ($directories as $dir) {
            // Ignore non-dir and non-regex paths
            if (!$dir->isDir()
                || !preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i', $dir->path())) {
                continue;
            }

            if ($this->repository->findOneByUuid($dir->path())) {
                continue;
            }

            $output->writeln('  - Removing '.$dir->path());

            if (!$input->getOption('dry-run')) {
                $this->storage->deleteDirectory($dir->path());
            }
        }

        return Command::SUCCESS;
    }
}
