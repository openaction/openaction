<?php

namespace App\Tests\Mailer;

use App\Entity\Registration;
use App\Entity\Upload;
use App\Mailer\PlatformMailer;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class PlatformMailerTest extends KernelTestCase
{
    public function testOrganizationInviteContextContainsOnlyScalars(): void
    {
        self::bootKernel();

        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $transport->reset();

        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $this->assertNotNull($organization);
        $organization->applyWhiteLabelUpdate(new Upload('branding/logo.png', null), 'Brand Space');

        $author = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'titouan.galopin@citipo.com']);
        $this->assertNotNull($author);

        $registration = new Registration('member@example.com', $organization, true, [], 'fr');

        static::getContainer()->get(PlatformMailer::class)->sendOrganizationInvite($organization, $registration, $author);

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var SendEmailMessage $sendEmailMessage */
        $sendEmailMessage = $messages[0]->getMessage();
        $email = $sendEmailMessage->getMessage();

        $context = $email->getContext();
        $this->assertContextIsScalar($context);
        $this->assertSame($registration->getLocale(), $context['locale']);
        $this->assertSame($organization->getName(), $context['organization_name']);
        $this->assertSame('Brand Space', $context['platform_name']);
        $this->assertStringContainsString('branding/logo.png', $context['platform_logo_url']);
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
