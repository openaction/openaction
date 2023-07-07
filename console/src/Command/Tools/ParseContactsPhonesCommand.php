<?php

namespace App\Command\Tools;

use App\Entity\Community\Contact;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Util\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:tools:parse-phones',
    description: 'Parse contacts phone numbers.',
)]
class ParseContactsPhonesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = $input->getOption('dry-run');

        $total = $this->em->getRepository(Contact::class)->count([]);
        $output->writeln('Processing '.$total.' contacts ...');

        $totalStrLength = strlen((string) $total);

        /** @var Contact[] $contacts */
        $contacts = $this->em->getRepository(Contact::class)->iterateAll();

        $i = 0;
        foreach ($contacts as $contact) {
            ++$i;

            $old = [
                'phone' => $contact->getContactPhone(),
                'parsedPhone' => $contact->getParsedContactPhone(),
                'workPhone' => $contact->getContactWorkPhone(),
                'parsedWorkPhone' => $contact->getParsedContactWorkPhone(),
            ];

            $contact->refreshParsedPhoneNumbers();

            $new = [
                'parsedPhone' => $contact->getParsedContactPhone(),
                'parsedWorkPhone' => $contact->getParsedContactWorkPhone(),
            ];

            $output->write('['.str_pad($i, $totalStrLength, ' ', STR_PAD_LEFT).'/'.$total.'] ');
            $output->write('Parsing [phone: "'.($old['phone'] ?: 'null').'" => ');
            $output->write('"'.($new['parsedPhone'] ? PhoneNumber::format($new['parsedPhone']) : 'null').'"]');
            $output->write(' [workPhone: "'.($old['workPhone'] ?: 'null').'" => ');
            $output->write('"'.($new['parsedWorkPhone'] ? PhoneNumber::format($new['parsedWorkPhone']) : 'null').'"]');
            $output->writeln(' for contact '.$contact->getId());

            if (!$dryRun) {
                $this->em->persist($contact);
                $this->em->flush();

                $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));
            }

            $this->em->detach($contact);
        }

        return Command::SUCCESS;
    }
}
