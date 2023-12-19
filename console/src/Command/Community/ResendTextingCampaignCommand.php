<?php

namespace App\Command\Community;

use App\Community\Consumer\SendTextingCampaignMessage;
use App\Entity\Community\TextingCampaign;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:community:resend-texting-campaign',
    description: 'Resend a given texting campaign.',
)]
class ResendTextingCampaignCommand extends Command
{
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->em = $em;
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->addArgument('campaign-uuid', InputArgument::REQUIRED, 'Campaign UUID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $campaign = $this->em->getRepository(TextingCampaign::class)->findOneBy([
            'uuid' => $input->getArgument('campaign-uuid'),
        ]);

        if (!$campaign) {
            throw new \InvalidArgumentException('Campaign not found');
        }

        $this->bus->dispatch(new SendTextingCampaignMessage($campaign->getId()));

        $io->success('Sending message dispatched.');

        return Command::SUCCESS;
    }
}
