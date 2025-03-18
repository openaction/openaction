<?php

namespace App\Command\Community;

use App\Bridge\Mailchimp\MailchimpInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:community:sync-mailchimp-campaigns-reports',
    description: 'Synchronize the reports of Mailchimp campaigns.',
)]
class SyncMailchimpCampaignsReportsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailchimpInterface $mailchimp,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('sent-after', InputArgument::REQUIRED, 'Synchronize Mailchimp campaigns sent after ...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sentAfter = new \DateTime($input->getArgument('sent-after'));

        $db = $this->em->getConnection();

        $sql = '
            SELECT c.id, c.external_id, o.mailchimp_server_prefix, o.mailchimp_api_key
            FROM community_emailing_campaigns c
            LEFT JOIN projects p ON c.project_id = p.id
            LEFT JOIN organizations o ON p.organization_id = o.id
            WHERE o.email_provider = \'mailchimp\' 
              AND c.external_id IS NOT NULL
              AND c.sent_at >= ?
        ';

        $campaigns = $db->executeQuery($sql, [$sentAfter->format('Y-m-d H:i:s')])->fetchAllAssociative();

        if (!$campaigns) {
            $io->success('No campaign to synchronize');

            return Command::SUCCESS;
        }

        foreach ($campaigns as $campaign) {
            $io->write('Synchronizing '.$campaign['id']);

            $report = $this->mailchimp->getCampaignReport(
                $campaign['mailchimp_api_key'],
                $campaign['mailchimp_server_prefix'],
                $campaign['external_id'],
            );

            foreach ($report as $email => $activity) {
                $db->executeStatement('
                    UPDATE community_emailing_campaigns_messages 
                    SET sent = ?, opened = ?, clicked = ?
                    WHERE id = (
                        SELECT m.id 
                        FROM community_emailing_campaigns_messages m
                        LEFT JOIN community_contacts c ON m.contact_id = c.id
                        WHERE c.email = ? AND m.campaign_id = ?
                    )
                ', [
                    $activity['opens'] > 0 || $activity['clicks'] > 0 ? 'true' : 'false', // sent
                    $activity['opens'] > 0 || $activity['clicks'] > 0 ? 'true' : 'false', // opened
                    $activity['clicks'] > 0 ? 'true' : 'false', // clicked
                    $email,
                    $campaign['id'],
                ]);
            }
        }

        $io->success('Synchronized');

        return Command::SUCCESS;
    }
}
