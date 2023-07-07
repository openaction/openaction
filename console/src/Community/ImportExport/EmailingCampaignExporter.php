<?php

namespace App\Community\ImportExport;

use App\Entity\Community\EmailingCampaign;
use App\Repository\Community\EmailingCampaignRepository;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;

class EmailingCampaignExporter
{
    private EmailingCampaignRepository $repository;

    public function __construct(EmailingCampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    public function export(EmailingCampaign $campaign)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile('php://output');

        $writer->addRow(WriterEntityFactory::createRowFromArray([
            'email',
            'opens',
            'clicks',
            'id',
            'area',
            'profile_formal_title',
            'profile_first_name',
            'profile_middle_name',
            'profile_last_name',
            'profile_birthdate',
            'profile_company',
            'profile_job_title',
            'contact_phone',
            'contact_work_phone',
            'parsed_contact_phone',
            'parsed_contact_work_phone',
            'social_facebook',
            'social_twitter',
            'social_linked_in',
            'social_telegram',
            'social_whatsapp',
            'address_street_line1',
            'address_street_line2',
            'address_zip_code',
            'address_city',
            'address_country',
            'is_member',
            'settings_receive_newsletters',
            'settings_receive_sms',
            'settings_receive_calls',
            'metadata_tags',
            'metadata_comment',
            'created_at',
        ]));

        foreach ($this->repository->getExportData($campaign) as $contact) {
            $writer->addRow(WriterEntityFactory::createRowFromArray($contact));
        }

        $writer->close();
    }
}
