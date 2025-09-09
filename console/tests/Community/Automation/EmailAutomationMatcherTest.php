<?php

namespace App\Tests\Community\Automation;

use App\Community\Automation\EmailAutomationMatcher;
use App\Entity\Area;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\Tag;
use App\Tests\UnitTestCase;

class EmailAutomationMatcherTest extends UnitTestCase
{
    public function provideMatches()
    {
        /*
         * Invalid
         */

        yield 'invalid-new-contact' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT),
            'contact' => null,
            'expected' => false,
        ];

        yield 'invalid-new-form-answer' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_FORM_ANSWER),
            'contact' => null,
            'expected' => false,
        ];

        /*
         * Notifications
         */

        yield 'notification-new-contact' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, 'notification@citipo.com'),
            'contact' => null,
            'expected' => true,
        ];

        yield 'notification-new-contact-with-contact' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, 'notification@citipo.com'),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => true,
        ];

        yield 'notification-new-form-answer' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_FORM_ANSWER, 'notification@citipo.com'),
            'contact' => null,
            'expected' => true,
        ];

        yield 'notification-new-form-answer-with-contact' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_FORM_ANSWER, 'notification@citipo.com'),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => true,
        ];

        /*
         * Messages
         */

        // No filter
        yield 'message-new-contact' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => true,
        ];

        // Normal contact
        yield 'message-new-contact-normal-true' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'typeFilter' => EmailAutomation::TYPE_CONTACT,
            ]),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => true,
        ];

        $member = new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com');
        $member->changePassword('password');

        yield 'message-new-contact-normal-false' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'typeFilter' => EmailAutomation::TYPE_CONTACT,
            ]),
            'contact' => $member,
            'expected' => false,
        ];

        // Member
        $member = new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com');
        $member->changePassword('password');

        yield 'message-new-contact-member-true' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'typeFilter' => EmailAutomation::TYPE_MEMBER,
            ]),
            'contact' => $member,
            'expected' => true,
        ];

        yield 'message-new-contact-member-false' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'typeFilter' => EmailAutomation::TYPE_MEMBER,
            ]),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => false,
        ];

        // Area filter
        $france = new Area(1, null, Area::TYPE_COUNTRY, 'FR', 'France', null);
        $this->setProperty($france, 'treeLeft', 1);
        $this->setProperty($france, 'treeRight', 4);

        $idf = new Area(2, $france, Area::TYPE_PROVINCE, 'IDF', 'Ile de France', null);
        $this->setProperty($idf, 'treeLeft', 2);
        $this->setProperty($idf, 'treeRight', 3);

        $germany = new Area(3, null, Area::TYPE_COUNTRY, 'DE', 'Germany', null);
        $this->setProperty($germany, 'treeLeft', 5);
        $this->setProperty($germany, 'treeRight', 6);

        yield 'message-new-contact-area-true' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'areaFilter' => $france,
            ]),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com', $idf),
            'expected' => true,
        ];

        yield 'message-new-contact-area-false-reversed' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'areaFilter' => $idf,
            ]),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com', $france),
            'expected' => false,
        ];

        yield 'message-new-contact-area-false-outside' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'areaFilter' => $germany,
            ]),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com', $idf),
            'expected' => false,
        ];

        // Tag filter
        $tag = new Tag($this->createOrganization(1), 'MyTag');
        $this->setProperty($tag, 'id', 1);

        $contactWithTag = new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com');
        $contactWithTag->getMetadataTags()->add($tag);

        yield 'message-new-contact-tag-true' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'tagFilter' => $tag,
            ]),
            'contact' => $contactWithTag,
            'expected' => true,
        ];

        yield 'message-new-contact-tag-false' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_CONTACT, null, [
                'tagFilter' => $tag,
            ]),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => false,
        ];

        // Form answer
        yield 'message-new-form-answer' => [
            'automation' => $this->createAutomation(EmailAutomation::TRIGGER_NEW_FORM_ANSWER),
            'contact' => new Contact($this->createOrganization(1), 'titouan.galopin@citipo.com'),
            'expected' => true,
        ];
    }

    /**
     * @dataProvider provideMatches
     */
    public function testMatches(EmailAutomation $automation, ?Contact $contact, bool $expected)
    {
        $matcher = new EmailAutomationMatcher();
        $this->assertSame($expected, $matcher->matches($automation, $contact));
    }

    private function createAutomation(string $trigger, ?string $toEmail = null, array $details = [])
    {
        return EmailAutomation::createFixture(array_merge($details, [
            'orga' => $this->createOrganization(1),
            'trigger' => $trigger,
            'toEmail' => $toEmail,
            'name' => 'Automation',
            'subject' => 'Subject',
        ]));
    }
}
