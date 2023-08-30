<?php

namespace App\Command\Translation;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'translation:create-checkable-file',
    description: 'Create a file that can be checked using a standard text typos checker.',
)]
class CreateCheckableFileCommand extends Command
{
    private string $translationsDir;

    public function __construct(string $translationsDir)
    {
        parent::__construct();

        $this->translationsDir = $translationsDir;
    }

    protected function configure()
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'Locale to generate the file for');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = Finder::create()
            ->files()
            ->in($this->translationsDir)
            ->name('*.'.$input->getArgument('locale').'.yaml')
            ->sortByName()
        ;

        foreach ($files as $file) {
            $output->writeln('# '.$file->getFilename()."\n");

            foreach ($this->flatten(Yaml::parse($file->getContents())) as $value) {
                $output->writeln(trim($value)."\n");
            }

            $output->writeln("\n\n");
        }

        return Command::SUCCESS;
    }

    private function flatten($data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $result[$key] = $value;

                continue;
            }

            foreach ($this->flatten($value) as $vKey => $vValue) {
                $result[$key.'.'.$vKey] = $vValue;
            }
        }

        return $result;
    }
}
