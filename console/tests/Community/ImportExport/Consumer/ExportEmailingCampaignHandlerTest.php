<?php

namespace App\Tests\Community\ImportExport\Consumer;

use App\Community\ImportExport\Consumer\ExportEmailingCampaignHandler;
use App\Community\ImportExport\Consumer\ExportEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Repository\Community\EmailingCampaignRepository;
use App\Tests\WebTestCase;
use App\Util\Spreadsheet;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class ExportEmailingCampaignHandlerTest extends WebTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ExportEmailingCampaignHandler::class);
        $handler(new ExportEmailingCampaignMessage('fr', 'titouan.galopin@citipo.com', 0));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsumeValid()
    {
        $client = self::createClient();
        $this->authenticate($client);

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        $handler = static::getContainer()->get(ExportEmailingCampaignHandler::class);
        $handler(new ExportEmailingCampaignMessage('fr', 'titouan.galopin@citipo.com', $campaign->getId()));

        // Should have send email
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());

        /** @var TemplatedEmail $email */
        $email = $message->getMessage();
        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', (string) $email->getContext()['organization_uuid']);
        $this->assertSame('Citipo', $email->getContext()['organization_name']);
        $this->assertSame('fr', $email->getContext()['locale']);
        $this->assertArrayHasKey('export_pathname', $email->getContext());

        // Check download
        $client->request('GET', '/console/organization/219025aa-7fe2-4385-ad8f-31f386720d10/community/contacts/export/download/'.$email->getContext()['export_pathname']);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $client->getInternalResponse()->getHeader('Content-Type'));

        $tempFile = sys_get_temp_dir().'/citipo-tests-export-contacts.xlsx';

        try {
            file_put_contents($tempFile, $client->getInternalResponse()->getContent());
            $this->assertCount(4, Spreadsheet::open(new File($tempFile)));
        } finally {
            unlink($tempFile);
        }
    }
}
