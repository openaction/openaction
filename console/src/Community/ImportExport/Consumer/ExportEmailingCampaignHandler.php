<?php

namespace App\Community\ImportExport\Consumer;

use App\Bridge\Brevo\BrevoInterface;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Community\EmailingCampaign;
use App\Mailer\PlatformMailer;
use App\Repository\Community\EmailingCampaignRepository;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ExportEmailingCampaignHandler implements MessageHandlerInterface
{
    private const HEADERS = [
        'email' => 'Email',
        'sentAt' => "Date d'envoi",
        'opened' => 'Ouvert',
        'clicked' => 'Cliqué',
        'bounced' => 'Erreur',
        'unsubscribed' => 'Désinscrit',
    ];

    private EmailingCampaignRepository $repository;
    private BrevoInterface $brevo;
    private SluggerInterface $slugger;
    private CdnUploader $uploader;
    private PlatformMailer $mailer;
    private LoggerInterface $logger;

    public function __construct(EmailingCampaignRepository $r, BrevoInterface $b, SluggerInterface $s, CdnUploader $u, PlatformMailer $m, LoggerInterface $l)
    {
        $this->repository = $r;
        $this->brevo = $b;
        $this->slugger = $s;
        $this->uploader = $u;
        $this->mailer = $m;
        $this->logger = $l;
    }

    public function __invoke(ExportEmailingCampaignMessage $message)
    {
        if (!$campaign = $this->repository->find($message->getCampaignId())) {
            $this->logger->error('Campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        $filename = $this->createFilename($campaign);
        touch($filename);

        try {
            if ($this->isBrevoCampaign($campaign)) {
                $this->doBrevoExport($filename, $campaign);
            } else {
                $this->doExport($filename, $campaign);
            }

            // Upload on CDN
            $upload = $this->uploader->upload(CdnUploadRequest::createOrganizationPrivateFileRequest(new File($filename)));

            // Send exported notification
            $this->mailer->sendNotificationExportFinished(
                $message->getLocale(),
                $message->getEmail(),
                $campaign->getProject()->getOrganization(),
                $upload,
            );
        } finally {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        return true;
    }

    private function createFilename(EmailingCampaign $campaign): string
    {
        $extension = $this->isBrevoCampaign($campaign) ? 'csv' : 'xlsx';

        return sys_get_temp_dir().'/'.date('Y-m-d').'-'.$this->slugger->slug($campaign->getSubject())->lower().'-report.'.$extension;
    }

    private function doBrevoExport(string $filename, EmailingCampaign $campaign): void
    {
        $organization = $campaign->getProject()->getOrganization();
        $apiKey = trim((string) ($organization->getBrevoApiKey() ?? ''));
        $campaignExternalId = trim((string) ($campaign->getExternalId() ?? ''));

        if ('' === $apiKey || '' === $campaignExternalId) {
            $this->logger->error('Brevo campaign export skipped due to incomplete configuration', [
                'campaign_id' => $campaign->getId(),
                'organization_id' => $organization->getId(),
                'organization_provider' => $organization->getEmailProvider(),
                'has_api_key' => '' !== $apiKey,
                'has_campaign_external_id' => '' !== $campaignExternalId,
            ]);

            throw new \RuntimeException('Brevo campaign export requires both API key and campaign external ID.');
        }

        $exportContent = $this->brevo->exportEmailCampaignRecipients($apiKey, $campaignExternalId);

        if (false === file_put_contents($filename, $exportContent)) {
            throw new \RuntimeException('Brevo campaign export could not be written locally.');
        }
    }

    private function doExport(string $filename, EmailingCampaign $campaign): void
    {
        $writer = new Writer();
        $writer->openToFile($filename);

        $writer->addRow(Row::fromValues(array_values(self::HEADERS)));

        foreach ($this->repository->getExportData($campaign) as $contact) {
            $row = [];
            foreach (self::HEADERS as $key => $label) {
                $row[] = $contact[$key] ?? null;
            }

            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();
    }

    private function isBrevoCampaign(EmailingCampaign $campaign): bool
    {
        return 'brevo' === $campaign->getProject()->getOrganization()->getEmailProvider();
    }
}
