<?php

namespace App\Search\Consumer;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Repository\Community\TagRepository;
use App\Repository\ProjectRepository;
use App\Search\Model\BatchRequest;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ExportCrmBatchHandler extends AbstractCrmBatchHandler
{
    private const ATTRIBUTES_TO_RETRIEVE = [
        'id',

        // Contact details
        'email',
        'contact_phone',
        'status',

        // Profile
        'profile_formal_title',
        'profile_first_name',
        'profile_middle_name',
        'profile_last_name',
        'profile_company',
        'profile_job_title',
        'profile_birthdate',

        // Address
        'address_street_line1',
        'address_street_line2',
        'address_zip_code',
        'address_city',
        'address_country',

        // Area
        'area_zip_code_name',
        'area_district_name',
        'area_province_name',
        'area_country_code',

        // Socials
        'social_facebook',
        'social_twitter',
        'social_linked_in',
        'social_telegram',
        'social_whatsapp',

        // Settings
        'settings_receive_newsletters',
        'settings_receive_sms',
        'settings_receive_calls',

        // Details
        'tags_names',
        'projects_names',
        'created_at',
    ];

    private const AREA_NAME_PRIORITY = [
        'area_zip_code_name',
        'address_zip_code',
        'area_district_name',
        'area_province_name',
        'address_country',
        'area_country_code',
    ];

    public function __construct(
        private SluggerInterface $slugger,
        private TagRepository $tagRepository,
        private ProjectRepository $projectRepository,
        private CdnUploader $cdnUploader,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function __invoke(ExportCrmBatchMessage $message)
    {
        if (!$orga = $this->getOrganizationRepository()->find($message->getOrganizationId())) {
            return true;
        }

        $orgaSlug = $this->slugger->slug($orga->getName())->lower();
        $orgaTagsNames = $this->tagRepository->findNamesByOrganization($orga);
        $orgaProjectsNames = $this->projectRepository->createCrmNamesRegistry($orga);

        // Fetch batch
        $batch = $this->createBatchIterable($orga, BatchRequest::createFromPayload($message->getBatchRequest()), 25_000, [
            'attributesToRetrieve' => self::ATTRIBUTES_TO_RETRIEVE, // Only retrieve attributes necessary for export
        ]);

        $steps = 0;
        $refreshSteps = 500;

        // Initialize exported file
        $filename = sys_get_temp_dir().'/'.date('Y-m-d').'-'.$orgaSlug.'-contacts.xlsx';
        touch($filename);

        $writer = new Writer();
        $writer->openToFile($filename);

        // Process the export
        $headerAdded = false;
        foreach ($batch as $document) {
            // Area name
            $areaName = '';
            foreach (self::AREA_NAME_PRIORITY as $field) {
                if ($document[$field] ?? null) {
                    $areaName = $document[$field];
                    break;
                }
            }

            $row = [
                'email' => $document['email'] ?: '',
                'id' => $document['id'],
                'area' => $areaName,
                'profile_formal_title' => $document['profile_formal_title'] ?: '',
                'profile_first_name' => $document['profile_first_name'] ?: '',
                'profile_middle_name' => $document['profile_middle_name'] ?: '',
                'profile_last_name' => $document['profile_last_name'] ?: '',
                'profile_birthdate' => $document['profile_birthdate'] ?: '',
                'profile_company' => $document['profile_company'] ?: '',
                'profile_job_title' => $document['profile_job_title'] ?: '',
                'contact_phone' => $document['contact_phone'] ?: '',
                'social_facebook' => $document['social_facebook'],
                'social_twitter' => $document['social_twitter'],
                'social_linked_in' => $document['social_linked_in'],
                'social_telegram' => $document['social_telegram'],
                'social_whatsapp' => $document['social_whatsapp'],
                'address_street_line1' => $document['address_street_line1'],
                'address_street_line2' => $document['address_street_line2'],
                'address_zip_code' => $document['address_zip_code'],
                'address_city' => $document['address_city'],
                'address_country' => $document['address_country'],
                'is_member' => 'm' === $document['status'],
                'settings_receive_newsletters' => $document['settings_receive_newsletters'],
                'settings_receive_sms' => $document['settings_receive_sms'],
                'settings_receive_calls' => $document['settings_receive_calls'],
                'tags' => implode(',', $document['tags_names']),
                'projects' => implode(',', $document['projects_names']),
                'created_at' => $document['created_at'],
            ];

            foreach ($orgaTagsNames as $tagName) {
                $row['Tag '.$tagName] = in_array($tagName, $document['tags_names'], true) ? '1' : '0';
            }

            foreach ($orgaProjectsNames as $projectName) {
                $row['Project '.$projectName] = in_array($projectName, $document['projects_names'], true) ? '1' : '0';
            }

            if (!$headerAdded) {
                $writer->addRow(Row::fromValues(array_keys($row)));
                $headerAdded = true;
            }

            $writer->addRow(Row::fromValues($row));

            // Advance job
            ++$steps;
            if (0 === $steps % $refreshSteps) {
                $this->getJobRepository()->advanceJobStep($message->getJobId(), $refreshSteps);
            }
        }

        $writer->close();

        // Upload file on CDN
        $upload = $this->cdnUploader->upload(
            CdnUploadRequest::createOrganizationPrivateFileRequest(new File($filename))
        );

        // Finish job
        $this->getJobRepository()->finishJob($message->getJobId(), [
            'fileUrl' => $this->router->generate('console_organization_community_contacts_batch_export_download', [
                'organizationUuid' => $orga->getUuid()->toRfc4122(),
                'pathname' => $upload->getPathname(),
            ]),
        ]);

        // Remove file
        @unlink($filename);

        return true;
    }
}
