<?php

namespace App\Command\Tools;

use App\Billing\Invoice\GenerateInvoicePdfMessage;
use App\Repository\Billing\OrderRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:tools:generate-missing-invoices',
    description: 'Generate and send invoices that weren\'t automatically generated yet.',
)]
class GenerateMissingInvoicesCommand extends Command
{
    public function __construct(private OrderRepository $repository, private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not generate/send anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching invoices to generate ...');

        $invoices = $this->repository->findInvoicesToGenerate();

        foreach ($invoices as $invoice) {
            $output->writeln('  - Dispatching message for #'.$invoice->getInvoiceNumber().' ...');

            if (!$input->getOption('dry-run')) {
                $this->bus->dispatch(new GenerateInvoicePdfMessage($invoice->getId()));
            }
        }

        $output->writeln('Done');

        return Command::SUCCESS;
    }
}
