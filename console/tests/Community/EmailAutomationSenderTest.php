<?php

namespace App\Tests\Community;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Brevo\MockBrevo;
use App\Community\EmailAutomationSender;
use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class EmailAutomationSenderTest extends KernelTestCase
{
    public function testSendWithBrevoRendersHtmlBeforeTransactionalSendAndKeepsCustomVariables(): void
    {
        self::bootKernel();

        /** @var Organization $organization */
        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $this->assertInstanceOf(Organization::class, $organization);
        $organization->setEmailProvider('brevo');
        $organization->setBrevoApiKey('brevo_api_key');
        $organization->setBrevoSenderEmail('brevo@example.test');
        $organization->setCreditsBalance(1000);

        $automation = EmailAutomation::createFixture([
            'orga' => $organization,
            'name' => 'Brevo merge tags rendering',
            'trigger' => EmailAutomation::TRIGGER_NEW_CONTACT,
            'fromEmail' => 'contact@example.test',
            'fromName' => 'Test',
            'subject' => 'Brevo rendering test',
            'toEmail' => 'recipient@example.test',
            'content' => 'Known token: -form-title- / unresolved token: -unknown-token-',
            'enabled' => true,
        ]);

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($automation);
        $em->flush();

        /** @var EmailAutomationSender $sender */
        $sender = static::getContainer()->get(EmailAutomationSender::class);
        $this->assertTrue($sender->send($automation, null, [
            '-form-title-' => 'My form',
        ]));

        /** @var BrevoInterface $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);
        $this->assertSame(1, $brevo->sendTransactionalEmailCalls);
        $this->assertCount(1, $brevo->transactionalEmails);

        $sentEmail = $brevo->transactionalEmails[0];
        $this->assertStringContainsString('Known token: My form', $sentEmail['htmlContent']);
        $this->assertStringNotContainsString('-form-title-', $sentEmail['htmlContent']);
        $this->assertStringContainsString('-unknown-token-', $sentEmail['htmlContent']);
        $this->assertSame('My form', $sentEmail['customVariables']['-form-title-'] ?? null);
    }
}
