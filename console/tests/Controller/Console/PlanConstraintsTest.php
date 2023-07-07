<?php

namespace App\Tests\Controller\Console;

use App\Platform\Plans;
use App\Tests\WebTestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

/*
 * Tests plans contraints are properly enforced.
 */
class PlanConstraintsTest extends WebTestCase
{
    private const PROJECTS = [
        Plans::ESSENTIAL => '4c8d5792-79de-4408-b091-88dbd7c9232b',
        Plans::STANDARD => '946a3832-e80d-4075-ae79-9e375965220b',
        Plans::PREMIUM => '272879f0-3c42-457c-bedb-0b4391d9055b',
        Plans::ORGANIZATION => 'e816bcc6-0568-46d1-b0c5-917ce4810a87',
    ];

    public function provideToolsAccessibility()
    {
        $tools = [
            // Essential
            '/website/pages' => [Plans::ESSENTIAL, Plans::STANDARD, Plans::PREMIUM, Plans::ORGANIZATION],

            // Standard
            '/website/posts' => [Plans::STANDARD, Plans::PREMIUM, Plans::ORGANIZATION],
            '/website/documents' => [Plans::STANDARD, Plans::PREMIUM, Plans::ORGANIZATION],
            '/website/trombinoscope' => [Plans::STANDARD, Plans::PREMIUM, Plans::ORGANIZATION],
            '/configuration/social-networks/accounts' => [Plans::STANDARD, Plans::PREMIUM, Plans::ORGANIZATION],
            '/configuration/social-networks/sharers' => [Plans::STANDARD, Plans::PREMIUM, Plans::ORGANIZATION],

            // Premium
            '/website/events' => [Plans::PREMIUM, Plans::ORGANIZATION],
            '/website/forms' => [Plans::PREMIUM, Plans::ORGANIZATION],

            // Organization
            '/developers/access' => [Plans::ORGANIZATION],
        ];

        $slugger = new AsciiSlugger();
        foreach (self::PROJECTS as $plan => $projectId) {
            foreach ($tools as $endpoint => $toolPlans) {
                yield $plan.'-'.$slugger->slug($endpoint)->lower() => [
                    '/console/project/'.$projectId.$endpoint,
                    in_array($plan, $toolPlans, true) ? 200 : 403,
                ];
            }
        }
    }

    /**
     * @dataProvider provideToolsAccessibility
     */
    public function testToolsAccessibility(string $url, int $expectedStatus)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame($expectedStatus);
    }

    public function provideContactHistory()
    {
        $contacts = [
            Plans::ESSENTIAL => ['9cf2236c-732f-46c2-b65e-78a3bed256ca', false],
            Plans::STANDARD => ['6315217a-381a-40df-a505-6505d0a68ee9', false],
            Plans::PREMIUM => ['7cea60a5-dc7b-4677-8458-b0fe75c844bf', true],
            Plans::ORGANIZATION => ['20e51b91-bdec-495d-854d-85d6e74fc75e', true],
        ];

        foreach ($contacts as $plan => $contact) {
            yield $plan => [
                '/console/project/'.self::PROJECTS[$plan].'/community/contacts/'.$contact[0].'/view',
                $contact[1],
            ];
        }
    }

    /**
     * @dataProvider provideContactHistory
     */
    public function testContactHistory(string $url, bool $expectedAccess)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', $url);

        if ($expectedAccess) {
            $this->assertSelectorNotExists('h2:contains("Upgrade now")');
        } else {
            $this->assertSelectorExists('h2:contains("Upgrade now")');
        }
    }
}
