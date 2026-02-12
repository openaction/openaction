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
        $sentAfter = new \DateTimeImmutable($input->getArgument('sent-after'));
        $syncUntil = new \DateTimeImmutable();
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

        $campaignsByApiKey = [];
        foreach ($campaigns as $campaign) {
            $campaignsByApiKey[(string) $campaign['brevo_api_key']][] = $campaign;
        }

        foreach ($campaignsByApiKey as $apiKey => $apiKeyCampaigns) {
            $campaignsStats = $this->brevo->getEmailCampaignsStats(
                $apiKey,
                $sentAfter,
                $syncUntil,
            );

            foreach ($apiKeyCampaigns as $campaign) {
                $io->write('Synchronizing '.$campaign['id']);

                $externalCampaignIds = $this->extractExternalCampaignIds((string) $campaign['external_id']);
                $aggregatedStats = $this->aggregateGlobalStats($campaignsStats, $externalCampaignIds);

                $db->executeStatement('
                    UPDATE community_emailing_campaigns
                    SET global_stats_sent = ?, global_stats_opened = ?, global_stats_clicked = ?
                    WHERE id = ?
                ', [
                    $aggregatedStats['sent'],
                    $aggregatedStats['opened'],
                    $aggregatedStats['clicked'],
                    $campaign['id'],
                ]);
            }
        }

        $io->success('Synchronized');

        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function extractExternalCampaignIds(string $externalId): array
    {
        $externalId = trim($externalId);

        if ('' === $externalId) {
            return [];
        }

        if (str_starts_with($externalId, '[')) {
            $decoded = json_decode($externalId, true);

            if (is_array($decoded)) {
                $externalIds = array_map(static fn (mixed $id): string => trim((string) $id), $decoded);

                return array_values(array_unique(array_filter($externalIds, static fn (string $id): bool => '' !== $id)));
            }
        }

        $externalIds = array_map('trim', explode(',', $externalId));

        return array_values(array_unique(array_filter($externalIds, static fn (string $id): bool => '' !== $id)));
    }

    /**
     * @param string[]                            $externalCampaignIds
     * @param array<string, array<string, mixed>> $campaignsStats
     *
     * @return array{sent: ?int, opened: ?int, clicked: ?int}
     */
    private function aggregateGlobalStats(array $campaignsStats, array $externalCampaignIds): array
    {
        $sent = 0;
        $opened = 0;
        $clicked = 0;
        $matched = false;

        foreach ($externalCampaignIds as $externalCampaignId) {
            $globalStats = $campaignsStats[$externalCampaignId] ?? null;

            if (!is_array($globalStats)) {
                continue;
            }

            $matched = true;
            $sent += $this->extractStatValue($globalStats, ['delivered', 'sent']);
            $opened += $this->extractStatValue($globalStats, ['uniqueViews', 'uniqueOpens', 'viewed', 'opens']);
            $clicked += $this->extractStatValue($globalStats, ['uniqueClicks', 'clickers', 'clicks']);
        }

        if (!$matched) {
            return ['sent' => null, 'opened' => null, 'clicked' => null];
        }

        return [
            'sent' => $sent,
            'opened' => $opened,
            'clicked' => $clicked,
        ];
    }

    /**
     * @param array<string, mixed> $globalStats
     * @param string[]             $keys
     */
    private function extractStatValue(array $globalStats, array $keys): int
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $globalStats)) {
                continue;
            }

            $value = $globalStats[$key];

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return 0;
    }
}
