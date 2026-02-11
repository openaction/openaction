<?php

namespace App\Tests\Bridge\Brevo;

use App\Bridge\Brevo\Brevo;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Entity\Project;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\CreateList;
use Brevo\Client\Model\CreateModel;
use Brevo\Client\Model\EmailExportRecipients;
use Brevo\Client\Model\GetFolders;
use Brevo\Client\Model\RequestContactImport;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;

enum TestContactFormalTitle: string
{
    case MR = 'Mr';
}

class BrevoTest extends TestCase
{
    public function testGetCampaignReportAggregatesExports()
    {
        $bridge = new class(new NullLogger(), new MockHttpClient(), 'openaction') extends Brevo {
            public array $exports = [];

            protected function fetchExportedEmails($emailCampaignsApi, $processApi, string $campaignId, string $recipientsType): array
            {
                return $this->exports[$recipientsType] ?? [];
            }

            protected function createConfiguration(string $apiKey): Configuration
            {
                return new Configuration();
            }
        };

        $bridge->exports = [
            EmailExportRecipients::RECIPIENTS_TYPE_ALL => ['click@example.test', 'open@example.test'],
            EmailExportRecipients::RECIPIENTS_TYPE_OPENERS => ['open@example.test'],
            EmailExportRecipients::RECIPIENTS_TYPE_CLICKERS => ['click@example.test'],
            EmailExportRecipients::RECIPIENTS_TYPE_SOFT_BOUNCES => [],
            EmailExportRecipients::RECIPIENTS_TYPE_HARD_BOUNCES => ['bounce@example.test'],
        ];

        $report = $bridge->getCampaignReport('token', '42');

        $this->assertSame([
            'click@example.test' => [
                'sent' => true,
                'opened' => true,
                'clicked' => true,
                'bounced' => false,
            ],
            'open@example.test' => [
                'sent' => true,
                'opened' => true,
                'clicked' => false,
                'bounced' => false,
            ],
            'bounce@example.test' => [
                'sent' => false,
                'opened' => false,
                'clicked' => false,
                'bounced' => true,
            ],
        ], $report);
    }

    public function testCampaignListNameUsesNamespaceAndCampaignId()
    {
        $bridge = new class(new NullLogger(), new MockHttpClient(), 'my-app') extends Brevo {
            public function exposeCampaignListName(EmailingCampaign $campaign): string
            {
                return $this->getCampaignListName($campaign);
            }
        };

        $campaign = $this->createMock(EmailingCampaign::class);
        $campaign->method('getId')->willReturn(42);

        $this->assertSame('my-app-campaign-42', $bridge->exposeCampaignListName($campaign));
    }

    public function testBuildCampaignBodyDoesNotSetTag()
    {
        $organization = $this->createMock(Organization::class);
        $organization->method('getName')->willReturn('OpenAction');
        $organization->method('getBrevoSenderEmail')->willReturn('sender@example.test');

        $project = $this->createMock(Project::class);
        $project->method('getOrganization')->willReturn($organization);

        $campaign = $this->createMock(EmailingCampaign::class);
        $campaign->method('getProject')->willReturn($project);
        $campaign->method('getFromName')->willReturn('Campaign Sender');
        $campaign->method('getSubject')->willReturn('Campaign Subject');
        $campaign->method('getReplyToEmail')->willReturn(null);
        $campaign->method('getFullFromEmail')->willReturn('reply@example.test');
        $campaign->method('getPreview')->willReturn('Campaign preview');

        $bridge = new class(new NullLogger(), new MockHttpClient(), 'openaction') extends Brevo {
            public function exposeBuildCampaignBody(EmailingCampaign $campaign, string $htmlContent)
            {
                return $this->buildCampaignBody($campaign, $htmlContent);
            }
        };

        $body = $bridge->exposeBuildCampaignBody($campaign, '<p>Hello</p>');

        $this->assertNull($body->getTag());
        $this->assertSame('Campaign Subject', $body->getName());
        $this->assertSame('Campaign Subject', $body->getSubject());
        $this->assertSame('Campaign Sender', $body->getSender()->getName());
        $this->assertSame('sender@example.test', $body->getSender()->getEmail());
        $this->assertSame('reply@example.test', $body->getReplyTo());
        $this->assertSame('Campaign preview', $body->getPreviewText());
    }

    public function testCreateCampaignListUsesExistingFolderId()
    {
        $contactsApi = $this->createMock(ContactsApi::class);

        $contactsApi
            ->expects($this->once())
            ->method('getFolders')
            ->with('1', '0', 'asc')
            ->willReturn((new GetFolders())->setFolders([(object) ['id' => 42, 'name' => 'Default']]));

        $contactsApi
            ->expects($this->never())
            ->method('createFolder');

        $contactsApi
            ->expects($this->once())
            ->method('createList')
            ->willReturnCallback(function (CreateList $list) {
                $this->assertSame('openaction-campaign-12', $list->getName());
                $this->assertSame(42, $list->getFolderId());

                return (new CreateModel())->setId(99);
            });

        $campaign = $this->createMock(EmailingCampaign::class);
        $campaign->method('getId')->willReturn(12);

        $bridge = new class(new NullLogger(), new MockHttpClient(), 'openaction') extends Brevo {
            public function exposeCreateCampaignList(ContactsApi $contactsApi, EmailingCampaign $campaign): int
            {
                return $this->createCampaignList($contactsApi, $campaign);
            }
        };

        $this->assertSame(99, $bridge->exposeCreateCampaignList($contactsApi, $campaign));
    }

    public function testCreateCampaignListThrowsWhenNoFolderExists()
    {
        $contactsApi = $this->createMock(ContactsApi::class);

        $contactsApi
            ->expects($this->once())
            ->method('getFolders')
            ->with('1', '0', 'asc')
            ->willReturn((new GetFolders())->setFolders([]));

        $contactsApi
            ->expects($this->never())
            ->method('createFolder');

        $contactsApi
            ->expects($this->never())
            ->method('createList');

        $campaign = $this->createMock(EmailingCampaign::class);
        $campaign->method('getId')->willReturn(12);

        $bridge = new class(new NullLogger(), new MockHttpClient(), 'openaction') extends Brevo {
            public function exposeCreateCampaignList(ContactsApi $contactsApi, EmailingCampaign $campaign): int
            {
                return $this->createCampaignList($contactsApi, $campaign);
            }
        };

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Brevo error: no contact folder available.');
        $bridge->exposeCreateCampaignList($contactsApi, $campaign);
    }

    public function testSyncContactsSetsOnlyNonEmptyAttributes()
    {
        $capturedRequests = [];
        $contactsApi = $this->createMock(ContactsApi::class);
        $contactsApi
            ->expects($this->once())
            ->method('importContacts')
            ->willReturnCallback(function (RequestContactImport $request) use (&$capturedRequests) {
                $capturedRequests[] = $request;

                return null;
            });

        $bridge = new class(new NullLogger(), new MockHttpClient(), 'openaction') extends Brevo {
            public function exposeSyncContacts(ContactsApi $contactsApi, int $listId, array $contacts): void
            {
                $this->syncContacts($contactsApi, $listId, $contacts);
            }
        };

        $bridge->exposeSyncContacts($contactsApi, 99, [
            [
                'email' => 'John.Doe@Example.test',
                'phone' => '+33601020304',
                'formalTitle' => 'Mr',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'fullName' => 'John Doe',
                'gender' => 'male',
                'nationality' => 'FR',
                'company' => 'OpenAction',
                'jobTitle' => 'Engineer',
                'addressLine1' => '1 rue de Paris',
                'addressLine2' => 'Bat A',
                'postalCode' => '75000',
                'city' => 'Paris',
                'country' => 'FR',
            ],
            [
                'email' => 'jane.doe@example.test',
                'phone' => '',
                'firstName' => '  ',
            ],
            [
                'email' => null,
                'phone' => '+33611111111',
            ],
        ]);

        $this->assertCount(1, $capturedRequests);
        $this->assertSame([99], $capturedRequests[0]->getListIds());
        $this->assertTrue($capturedRequests[0]->getUpdateExistingContacts());
        $this->assertCount(2, $capturedRequests[0]->getJsonBody());

        $first = $capturedRequests[0]->getJsonBody()[0];
        $second = $capturedRequests[0]->getJsonBody()[1];

        $this->assertSame('john.doe@example.test', $first->getEmail());
        $this->assertSame([
            'PHONE' => '+33601020304',
            'FORMAL_TITLE' => 'Mr',
            'FIRST_NAME' => 'John',
            'LAST_NAME' => 'Doe',
            'FULL_NAME' => 'John Doe',
            'GENDER' => 'male',
            'NATIONALITY' => 'FR',
            'COMPANY' => 'OpenAction',
            'JOB_TITLE' => 'Engineer',
            'ADDRESS_LINE_1' => '1 rue de Paris',
            'ADDRESS_LINE_2' => 'Bat A',
            'POSTAL_CODE' => '75000',
            'CITY' => 'Paris',
            'COUNTRY' => 'FR',
        ], $first->getAttributes());

        $this->assertSame('jane.doe@example.test', $second->getEmail());
        $this->assertNull($second->getAttributes());
    }

    public function testSyncContactsNormalizesBackedEnums()
    {
        $capturedRequests = [];
        $contactsApi = $this->createMock(ContactsApi::class);
        $contactsApi
            ->expects($this->once())
            ->method('importContacts')
            ->willReturnCallback(function (RequestContactImport $request) use (&$capturedRequests) {
                $capturedRequests[] = $request;

                return null;
            });

        $bridge = new class(new NullLogger(), new MockHttpClient(), 'openaction') extends Brevo {
            public function exposeSyncContacts(ContactsApi $contactsApi, int $listId, array $contacts): void
            {
                $this->syncContacts($contactsApi, $listId, $contacts);
            }
        };

        $bridge->exposeSyncContacts($contactsApi, 99, [
            [
                'email' => 'jane.doe@example.test',
                'formalTitle' => TestContactFormalTitle::MR,
            ],
        ]);

        $this->assertCount(1, $capturedRequests);
        $this->assertCount(1, $capturedRequests[0]->getJsonBody());

        $first = $capturedRequests[0]->getJsonBody()[0];
        $this->assertSame('jane.doe@example.test', $first->getEmail());
        $this->assertSame(['FORMAL_TITLE' => 'Mr'], $first->getAttributes());
    }
}
