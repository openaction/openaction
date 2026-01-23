<?php

namespace App\Community\ImportExport\Consumer;

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
    private SluggerInterface $slugger;
    private CdnUploader $uploader;
    private PlatformMailer $mailer;
    private LoggerInterface $logger;

    public function __construct(EmailingCampaignRepository $r, SluggerInterface $s, CdnUploader $u, PlatformMailer $m, LoggerInterface $l)
    {
        $this->repository = $r;
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

        $filename = sys_get_temp_dir().'/'.date('Y-m-d').'-'.$this->slugger->slug($campaign->getSubject())->lower().'-report.xlsx';
        touch($filename);

        try {
            $this->doExport($filename, $campaign);

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
            unlink($filename);
        }

        return true;
    }

    private function doExport(string $filename, EmailingCampaign $campaign)
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
}
