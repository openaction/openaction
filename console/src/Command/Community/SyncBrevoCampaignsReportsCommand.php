<?php

namespace App\Command\Community;

use App\Bridge\Brevo\BrevoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:community:sync-brevo-campaigns-reports',
    description: 'Synchronize the reports of Brevo campaigns.',
)]
class SyncBrevoCampaignsReportsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BrevoInterface $brevo,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('sent-after', InputArgument::REQUIRED, 'Synchronize Brevo campaigns sent after ...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sentAfter = new \DateTime($input->getArgument('sent-after'));
        $db = $this->em->getConnection();

        $campaigns = $db->executeQuery('
            SELECT c.id, c.external_id, o.brevo_api_key
            FROM community_emailing_campaigns c
            LEFT JOIN projects p ON c.project_id = p.id
            LEFT JOIN organizations o ON p.organization_id = o.id
            WHERE o.email_provider = \'brevo\' 
              AND c.external_id IS NOT NULL
              AND o.brevo_api_key IS NOT NULL
              AND c.sent_at >= ?
        ', [$sentAfter->format('Y-m-d H:i:s')])->fetchAllAssociative();

        if (!$campaigns) {
            $io->success('No campaign to synchronize');

            return Command::SUCCESS;
        }

        foreach ($campaigns as $campaign) {
            $io->write('Synchronizing '.$campaign['id']);

            $report = $this->brevo->getCampaignReport(
                $campaign['brevo_api_key'],
                (string) $campaign['external_id'],
            );

            foreach ($report as $email => $activity) {
                $db->executeStatement('
                    UPDATE community_emailing_campaigns_messages 
                    SET sent = ?, opened = ?, clicked = ?, bounced = ?
                    WHERE id = (
                        SELECT m.id 
                        FROM community_emailing_campaigns_messages m
                        LEFT JOIN community_contacts c ON m.contact_id = c.id
                        WHERE c.email = ? AND m.campaign_id = ?
                    )
                ', [
                    $activity['sent'] ? 'true' : 'false',
                    $activity['opened'] ? 'true' : 'false',
                    $activity['clicked'] ? 'true' : 'false',
                    $activity['bounced'] ? 'true' : 'false',
                    $email,
                    $campaign['id'],
                ]);
            }
        }

        $io->success('Synchronized');

        return Command::SUCCESS;
    }
}
