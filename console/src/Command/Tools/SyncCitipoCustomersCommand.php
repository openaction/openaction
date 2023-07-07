<?php

namespace App\Command\Tools;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Api\Model\ContactApiData;
use App\Api\Persister\ContactApiPersister;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\User;
use App\Repository\Community\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:tools:sync-customers',
    description: 'Synchronize Citipo customers with the main organization\'s community.',
)]
class SyncCitipoCustomersCommand extends Command
{
    private EntityManagerInterface $em;
    private ContactApiPersister $persister;
    private MessageBusInterface $bus;
    private string $syncCustomersWith;

    public function __construct(EntityManagerInterface $em, ContactApiPersister $p, MessageBusInterface $bus, string $syncCustomersWith)
    {
        parent::__construct();

        $this->em = $em;
        $this->persister = $p;
        $this->bus = $bus;
        $this->syncCustomersWith = $syncCustomersWith;
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->syncCustomersWith) {
            $output->writeln('Synchronization disabled, ignoring.');
            exit;
        }

        $output->writeln('Fetching main organization ...');

        /** @var Organization $citipo */
        $citipo = $this->em->find(Organization::class, $this->syncCustomersWith);
        if (!$citipo) {
            throw new \InvalidArgumentException('Organization '.$this->syncCustomersWith.' does not exist.');
        }

        $mainProject = $citipo->getProjects()->first();

        // Fetching Citipo users
        $output->writeln('Fetching users ...');

        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->findBy([], ['id' => 'ASC']);

        // Persisting users as contacts of the Citipo organization
        $output->writeln('Synchronizing ...');

        /** @var ContactRepository $contactRepository */
        $contactRepository = $this->em->getRepository(Contact::class);

        foreach ($users as $user) {
            $output->writeln('  - Processing '.$user->getEmail().' ...');

            $output->write('    Clearing previous tags ... ');
            if ($contact = $contactRepository->findOneByMainEmail($citipo, $user->getEmail())) {
                $tagsIds = [];
                $tagsNames = [];

                foreach ($contact->getMetadataTags() as $tag) {
                    if (u($tag->getName())->lower()->startsWith('citipo:')) {
                        $tagsIds[] = $tag->getId();
                        $tagsNames[] = $tag->getName();
                    }
                }

                $output->write('('.implode(', ', $tagsNames).')');
                if (!$input->getOption('dry-run')) {
                    $contactRepository->clearTags($contact, $tagsIds);
                }
            }

            $output->write("\n    Persisting ... ");
            $payload = $this->createPayload($user);

            $output->write('('.implode(', ', $payload->metadataTags).")\n\n");
            if (!$input->getOption('dry-run')) {
                $this->persister->persist($this->createPayload($user), $mainProject, false);
            }
        }

        // Refresh stats only at the end
        $this->bus->dispatch(new RefreshContactStatsMessage($citipo->getId()));

        return Command::SUCCESS;
    }

    private function createPayload(User $user): ContactApiData
    {
        $data = new ContactApiData();
        $data->email = $user->getEmail();
        $data->profileFirstName = $user->getFirstName();
        $data->profileLastName = $user->getLastName();

        // Tags
        $data->metadataTags = ['citipo:customer'];

        // Add all orgas as tags
        $isAdmin = false;
        foreach ($user->getOrganizations() as $orga) {
            $data->metadataTags[] = 'citipo:orga:'.$orga->getName();

            if ($this->em->getRepository(OrganizationMember::class)->findMember($user, $orga)?->isAdmin()) {
                $isAdmin = true;
            }
        }

        // Mark users admins of at least one orgas as admin, others as collaborators
        $data->metadataTags[] = $isAdmin ? 'citipo:admin' : 'citipo:collaborator';

        // Users that used Citipo in the last 30 days are considered active, others are't
        $isActive = $user->getLastVisitDate() > new \DateTime('30 days ago');
        $data->metadataTags[] = $isActive ? 'citipo:active' : 'citipo:inactive';

        return $data;
    }
}
