<?php

namespace App\Tests\Community\Consumer;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Brevo\MockBrevo;
use App\Community\Consumer\SendBrevoEmailingCampaignHandler;
use App\Community\Consumer\SendBrevoEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;

class SendBrevoEmailingCampaignHandlerTest extends KernelTestCase
{
    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        /** @var Organization $organization */
        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $organization->setEmailProvider('brevo');
        $organization->setBrevoApiKey('brevo_api_key');
        $organization->setBrevoSenderEmail('brevo@example.test');
        static::getContainer()->get('doctrine')->getManager()->flush();

        $this->assertSame(0, static::getContainer()->get(EmailingCampaignMessageRepository::class)->count(['campaign' => $campaign]));

        /** @var BrevoInterface $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId()));

        $this->assertCount(1, $brevo->campaigns);
        $payload = $brevo->campaigns['1'];
        $this->assertSame('1', $campaign->getExternalId());
        $this->assertNotNull($campaign->getResolvedAt());
        $this->assertCount(3, $payload['contacts']);
        $this->assertStringContainsString('Hello world', $payload['html']);
        foreach ($payload['contacts'] as $contact) {
            $this->assertArrayHasKey('email', $contact);
            $this->assertArrayHasKey('phone', $contact);
            $this->assertArrayHasKey('formalTitle', $contact);
            $this->assertArrayHasKey('firstName', $contact);
            $this->assertArrayHasKey('lastName', $contact);
            $this->assertArrayHasKey('fullName', $contact);
            $this->assertArrayHasKey('gender', $contact);
            $this->assertArrayHasKey('nationality', $contact);
            $this->assertArrayHasKey('company', $contact);
            $this->assertArrayHasKey('jobTitle', $contact);
            $this->assertArrayHasKey('addressLine1', $contact);
            $this->assertArrayHasKey('addressLine2', $contact);
            $this->assertArrayHasKey('postalCode', $contact);
            $this->assertArrayHasKey('city', $contact);
            $this->assertArrayHasKey('country', $contact);
        }

        $messages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);

        foreach ($messages as $message) {
            $this->assertTrue($message->isSent());
        }
    }
}
