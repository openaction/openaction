<?php

namespace App\Tests\Mailer;

use App\Entity\Community\Contact;
use App\Entity\Community\ContactUpdate;
use App\Mailer\OrganizationMailer;
use App\Proxy\DomainRouter;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;
use App\Util\Uid;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class OrganizationMailerTest extends KernelTestCase
{
    public function testUnregisterContextContainsOnlyScalars(): void
    {
        self::bootKernel();

        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $transport->reset();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('e816bcc6-0568-46d1-b0c5-917ce4810a87');
        $this->assertNotNull($project);

        $contact = new Contact($project->getOrganization(), 'member@example.com');
        $contactUpdate = ContactUpdate::createUnregister($contact);

        static::getContainer()->get(OrganizationMailer::class)->sendUnregisterConfirm($project, $contactUpdate);

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var SendEmailMessage $sendEmailMessage */
        $sendEmailMessage = $messages[0]->getMessage();
        $email = $sendEmailMessage->getMessage();

        $context = $email->getContext();
        $this->assertContextIsScalar($context);
        $this->assertSame($project->getWebsiteLocale(), $context['locale']);
        $this->assertSame($project->getOrganization()->getName(), $context['organization_name']);

        $reference = Uid::toBase62($contactUpdate->getUuid()).'/'.$contactUpdate->getToken();
        $redirectUrl = static::getContainer()->get(DomainRouter::class)->generateRedirectUrl($project, 'unregister-confirm', $reference);
        $this->assertSame($redirectUrl, $context['redirect_url']);
        $this->assertSame(static::getContainer()->get(DomainRouter::class)->generateUrl($project, '/'), $context['homepage_url']);
    }

    private function assertContextIsScalar(array $context): void
    {
        foreach ($context as $key => $value) {
            $this->assertTrue(
                $this->isScalarish($value),
                sprintf('Context value for "%s" is not scalar, got %s', $key, get_debug_type($value))
            );
        }
    }

    private function isScalarish($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $child) {
                if (!$this->isScalarish($child)) {
                    return false;
                }
            }

            return true;
        }

        return is_scalar($value) || null === $value;
    }
}
