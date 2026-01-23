<?php

namespace App\Community\ImportExport\Consumer;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Organization;
use App\Mailer\PlatformMailer;
use App\Repository\Community\ContactRepository;
use App\Repository\OrganizationRepository;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class ExportHandler implements MessageHandlerInterface
{
    private const HEADERS = [
        'email' => 'Email',
        'contactAdditionalEmails' => 'Emails secondaires (séparés par des virgules)',
        'profileFormalTitle' => 'Civilité',
        'profileFirstName' => 'Prénom',
        'profileMiddleName' => 'Deuxième prénom',
        'profileLastName' => 'Nom',
        'birthName' => 'Nom de naissance',
        'profileBirthdate' => 'Date de naissance',
        'isDeceased' => 'Décédé',
        'profileGender' => 'Genre',
        'profileNationality' => 'Nationalité',
        'birthCity' => 'Ville de naissance',
        'birthCountryCode' => 'Pays de naissance',
        'profileCompany' => 'Organisation',
        'profileJobTitle' => 'Rôle',
        'accountLanguage' => 'Langue préférée',
        'contactPhone' => 'Téléphone',
        'contactWorkPhone' => 'Téléphone pro',
        'socialFacebook' => 'Facebook',
        'socialTwitter' => 'Twitter',
        'socialLinkedIn' => 'LinkedIn',
        'socialInstagram' => 'Instagram',
        'socialTikTok' => 'TikTok',
        'socialBluesky' => 'Bluesky',
        'socialTelegram' => 'Telegram',
        'socialWhatsapp' => 'WhatsApp',
        'addressStreetNumber' => 'Numéro de rue',
        'addressStreetLine1' => 'Adresse ligne 1',
        'addressStreetLine2' => 'Adresse ligne 2',
        'addressZipCode' => 'Code postal',
        'addressCity' => 'Ville',
        'addressCountry' => 'Pays',
        'settingsReceiveNewsletters' => 'Abonnement newsletter',
        'settingsReceiveSms' => 'Abonnement SMS',
        'settingsReceiveCalls' => 'Abonnement appels',
        'metadataTags' => 'Tags (séparés par des virgules)',
        'metadataSource' => 'Source',
        'metadataComment' => 'Commentaire',
        'metadataCustomFields' => 'Champs personnalisés',
        'recruitedBy' => 'Recruté par',
    ];

    private OrganizationRepository $orgaRepository;
    private ContactRepository $contactRepository;
    private CdnUploader $uploader;
    private PlatformMailer $mailer;
    private LoggerInterface $logger;

    public function __construct(OrganizationRepository $or, ContactRepository $cr, CdnUploader $u, PlatformMailer $m, LoggerInterface $l)
    {
        $this->orgaRepository = $or;
        $this->contactRepository = $cr;
        $this->uploader = $u;
        $this->mailer = $m;
        $this->logger = $l;
    }

    public function __invoke(ExportMessage $message)
    {
        if (!$orga = $this->orgaRepository->find($message->getOrganizationId())) {
            $this->logger->error('Organization not found by its ID', ['id' => $message->getOrganizationId()]);

            return true;
        }

        $filename = sys_get_temp_dir().'/'.date('Y-m-d').'-'.(new AsciiSlugger())->slug($orga->getName())->lower().'-contacts.xlsx';
        touch($filename);

        try {
            $this->doExport($filename, $orga, $message->getTagId());

            // Upload on CDN
            $upload = $this->uploader->upload(CdnUploadRequest::createOrganizationPrivateFileRequest(new File($filename)));

            // Send uploaded notification
            $this->mailer->sendNotificationExportFinished($message->getLocale(), $message->getEmail(), $orga, $upload);
        } finally {
            unlink($filename);
        }

        return true;
    }

    private function doExport(string $filename, Organization $organization, ?int $tagId = null)
    {
        $writer = new Writer();
        $writer->openToFile($filename);

        $headerAdded = false;
        foreach ($this->contactRepository->getExportData($organization, $tagId) as $contact) {
            if (!$headerAdded) {
                $writer->addRow(Row::fromValues(array_values(self::HEADERS)));
                $headerAdded = true;
            }

            $row = [];
            foreach (self::HEADERS as $key => $label) {
                $row[] = $contact[$key] ?? null;
            }

            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();
    }
}
