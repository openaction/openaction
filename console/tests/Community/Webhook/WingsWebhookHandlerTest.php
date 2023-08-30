<?php

namespace App\Tests\Community\Webhook;

use App\Community\Webhook\WingsWebhookHandler;
use App\Community\Webhook\WingsWebhookMessage;
use App\Entity\Community\Contact;
use App\Entity\Project;
use App\Repository\Community\ContactRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;
use App\Util\Json;

class WingsWebhookHandlerTest extends KernelTestCase
{
    public function provideWebhook()
    {
        yield 'attendance_created' => [file_get_contents(__DIR__.'/../../Fixtures/wings/attendance_created.json'), 'attendance'];
        yield 'donation_created' => [file_get_contents(__DIR__.'/../../Fixtures/wings/donation_created.json'), 'donation'];
        yield 'signature_created' => [file_get_contents(__DIR__.'/../../Fixtures/wings/signature_created.json'), 'petition'];
        yield 'signature_confirmed' => [file_get_contents(__DIR__.'/../../Fixtures/wings/signature_confirmed.json'), 'petition'];
        yield 'signup_created' => [file_get_contents(__DIR__.'/../../Fixtures/wings/signup_created.json'), 'signup'];
        yield 'submission_created' => [file_get_contents(__DIR__.'/../../Fixtures/wings/submission_created.json'), 'submission'];
    }

    /**
     * @dataProvider provideWebhook
     */
    public function testWebhook(string $content, string $expectedTagName)
    {
        self::bootKernel();

        $this->assertNull(static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'test@citipo.com']));

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('e816bcc6-0568-46d1-b0c5-917ce4810a87');

        $handler = static::getContainer()->get(WingsWebhookHandler::class);
        $handler(new WingsWebhookMessage($project->getId(), Json::decode($content)));

        // Contact should have been created
        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'test@citipo.com']);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('wings', $contact->getMetadataSource());
        $this->assertSame('test@citipo.com', $contact->getEmail());
        $this->assertSame('First name', $contact->getProfileFirstName());
        $this->assertSame('Last name', $contact->getProfileLastName());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertSame([$expectedTagName], $contact->getMetadataTagsNames());
    }
}
