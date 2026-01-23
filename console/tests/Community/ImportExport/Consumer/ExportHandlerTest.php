<?php

namespace App\Tests\Community\ImportExport\Consumer;

use App\Community\ImportExport\Consumer\ExportHandler;
use App\Community\ImportExport\Consumer\ExportMessage;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use App\Util\Spreadsheet;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class ExportHandlerTest extends WebTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ExportHandler::class);
        $handler(new ExportMessage('fr', 'titouan.galopin@citipo.com', 0, null));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsumeValidFull()
    {
        $client = self::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $this->assertInstanceOf(Organization::class, $orga);

        $handler = static::getContainer()->get(ExportHandler::class);
        $handler(new ExportMessage('fr', 'titouan.galopin@citipo.com', $orga->getId(), null));

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
            $this->assertCount(7, $sheet = Spreadsheet::open(new File($tempFile)));
            $rows = $sheet->getFirstLines(2);
            $this->assertSame([
                'Email',
                'Emails secondaires (séparés par des virgules)',
                'Civilité',
                'Prénom',
                'Deuxième prénom',
                'Nom',
                'Nom de naissance',
                'Date de naissance',
                'Décédé',
                'Genre',
                'Nationalité',
                'Ville de naissance',
                'Pays de naissance',
                'Organisation',
                'Rôle',
                'Langue préférée',
                'Téléphone',
                'Téléphone pro',
                'Facebook',
                'Twitter',
                'LinkedIn',
                'Instagram',
                'TikTok',
                'Bluesky',
                'Telegram',
                'WhatsApp',
                'Numéro de rue',
                'Adresse ligne 1',
                'Adresse ligne 2',
                'Code postal',
                'Ville',
                'Pays',
                'Abonnement newsletter',
                'Abonnement SMS',
                'Abonnement appels',
                'Tags (séparés par des virgules)',
                'Source',
                'Commentaire',
                'Champs personnalisés',
                'Recruté par',
            ], array_values($rows[0]));
            $this->assertCount(40, $rows[1]);
        } finally {
            unlink($tempFile);
        }
    }

    public function testConsumeValidTag()
    {
        $client = self::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $this->assertInstanceOf(Organization::class, $orga);

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);
        $this->assertInstanceOf(Tag::class, $tag);

        $handler = static::getContainer()->get(ExportHandler::class);
        $handler(new ExportMessage('en', 'titouan.galopin@citipo.com', $orga->getId(), $tag->getId()));

        // Should have send email
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());

        /** @var TemplatedEmail $email */
        $email = $message->getMessage();
        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', (string) $email->getContext()['organization_uuid']);
        $this->assertSame('Citipo', $email->getContext()['organization_name']);
        $this->assertSame('en', $email->getContext()['locale']);
        $this->assertArrayHasKey('export_pathname', $email->getContext());

        // Check download
        $client->request('GET', '/console/organization/219025aa-7fe2-4385-ad8f-31f386720d10/community/contacts/export/download/'.$email->getContext()['export_pathname']);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $client->getInternalResponse()->getHeader('Content-Type'));

        $tempFile = sys_get_temp_dir().'/citipo-tests-export-contacts.xlsx';

        try {
            file_put_contents($tempFile, $client->getInternalResponse()->getContent());
            $this->assertCount(4, $sheet = Spreadsheet::open(new File($tempFile)));
            $rows = $sheet->getFirstLines(2);
            $this->assertSame([
                'Email',
                'Emails secondaires (séparés par des virgules)',
                'Civilité',
                'Prénom',
                'Deuxième prénom',
                'Nom',
                'Nom de naissance',
                'Date de naissance',
                'Décédé',
                'Genre',
                'Nationalité',
                'Ville de naissance',
                'Pays de naissance',
                'Organisation',
                'Rôle',
                'Langue préférée',
                'Téléphone',
                'Téléphone pro',
                'Facebook',
                'Twitter',
                'LinkedIn',
                'Instagram',
                'TikTok',
                'Bluesky',
                'Telegram',
                'WhatsApp',
                'Numéro de rue',
                'Adresse ligne 1',
                'Adresse ligne 2',
                'Code postal',
                'Ville',
                'Pays',
                'Abonnement newsletter',
                'Abonnement SMS',
                'Abonnement appels',
                'Tags (séparés par des virgules)',
                'Source',
                'Commentaire',
                'Champs personnalisés',
                'Recruté par',
            ], array_values($rows[0]));
            $this->assertCount(40, $rows[1]);
        } finally {
            unlink($tempFile);
        }
    }
}
