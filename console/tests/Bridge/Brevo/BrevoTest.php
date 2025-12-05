<?php

namespace App\Tests\Bridge\Brevo;

use App\Bridge\Brevo\Brevo;
use Brevo\Client\Configuration;
use Brevo\Client\Model\EmailExportRecipients;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;

class BrevoTest extends TestCase
{
    public function testGetCampaignReportAggregatesExports()
    {
        $bridge = new class(new NullLogger(), new MockHttpClient()) extends Brevo {
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
}
