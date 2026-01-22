<?php

namespace App\Tests\Controller\Console\Project\Community\Emailing;

use App\Community\ImportExport\Consumer\ExportEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Repository\Community\EmailingCampaignRepository;
use App\Tests\WebTestCase;
use App\Util\Json;

class StatsControllerTest extends WebTestCase
{
    public function provideStats()
    {
        yield 'Campaign without tracking' => [
            'uuid' => '95b3f576-c643-45ba-9d5e-c9c44f65fab8',
            'expected' => ['total' => 3, 'sent' => 2, 'opened' => 2, 'clicked' => 2],
        ];

        yield 'Campaign with opens tracking' => [
            'uuid' => 'e0340fcd-f0ec-4ee8-b0f9-7545f3a53cc5',
            'expected' => ['total' => 1, 'sent' => 1, 'opened' => 1, 'clicked' => 0],
        ];

        yield 'Campaign with clicks tracking' => [
            'uuid' => '9e2f34f9-de81-4303-8f39-cb5a89183316',
            'expected' => ['total' => 1, 'sent' => 1, 'opened' => 1, 'clicked' => 1],
        ];

        yield 'Campaign with known stats' => [
            'uuid' => '06afd13a-ede2-4d46-9c8c-3ad80356c41f',
            'expected' => ['total' => 1, 'sent' => 0, 'opened' => 0, 'clicked' => 0],
        ];
    }

    /**
     * @dataProvider provideStats
     */
    public function testStats(string $uuid, array $expectedStats)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.$uuid.'/stats');
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertSame($expectedStats, Json::decode($client->getResponse()->getContent()));
    }

    /**
     * @dataProvider provideStats
     */
    public function testReport(string $uuid)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.$uuid.'/report');
        $this->assertResponseIsSuccessful();
    }

    public function provideReportSearch()
    {
        /*
         * Test sorting and full mapping
         */

        yield 'sort-clicks-desc' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'filter' => [],
                'sort' => [
                    ['colId' => 'clicks', 'sort' => 'desc'],
                ],
            ],
            'expected' => [
                'total' => 3,
                'contacts' => [
                    [
                        'id' => '20e51b91-bdec-495d-854d-85d6e74fc75e',
                        'url' => '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view',
                        'type' => 'c',
                        'email' => 'olivie.gregoire@gmail.com',
                        'hash' => '21ac5d5fef034b041dceb4fce3d3995c',
                        'firstName' => 'Olivie',
                        'lastName' => 'Gregoire',
                        'subscribed' => true,
                        'location' => 'France',
                        'opens' => 3,
                        'clicks' => 3,
                    ],
                    [
                        'email' => 'a.compagnon@protonmail.com',
                        'opens' => 4,
                        'clicks' => 2,
                    ],
                    [
                        'email' => 'julien.dubois@exampleco.com',
                        'opens' => 0,
                        'clicks' => 0,
                    ],
                ],
            ],
        ];

        yield 'sort-opens-asc' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'filter' => [],
                'sort' => [
                    ['colId' => 'opens', 'sort' => 'asc'],
                ],
            ],
            'expected' => [
                'total' => 3,
                'contacts' => [
                    ['email' => 'julien.dubois@exampleco.com', 'opens' => 0, 'clicks' => 0],
                    ['email' => 'olivie.gregoire@gmail.com', 'opens' => 3, 'clicks' => 3],
                    ['email' => 'a.compagnon@protonmail.com', 'opens' => 4, 'clicks' => 2],
                ],
            ],
        ];

        yield 'sort-email-desc' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'filter' => [],
                'sort' => [
                    ['colId' => 'email', 'sort' => 'desc'],
                ],
            ],
            'expected' => [
                'total' => 3,
                'contacts' => [
                    ['email' => 'olivie.gregoire@gmail.com', 'opens' => 3, 'clicks' => 3],
                    ['email' => 'julien.dubois@exampleco.com', 'opens' => 0, 'clicks' => 0],
                    ['email' => 'a.compagnon@protonmail.com', 'opens' => 4, 'clicks' => 2],
                ],
            ],
        ];

        yield 'sort-firstName-desc' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'filter' => [],
                'sort' => [
                    ['colId' => 'firstName', 'sort' => 'desc'],
                ],
            ],
            'expected' => [
                'total' => 3,
                'contacts' => [
                    ['firstName' => null],
                    ['firstName' => 'Olivie'],
                    ['firstName' => 'André'],
                ],
            ],
        ];

        yield 'sort-lastName-asc' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'filter' => [],
                'sort' => [
                    ['colId' => 'lastName', 'sort' => 'asc'],
                ],
            ],
            'expected' => [
                'total' => 3,
                'contacts' => [
                    ['lastName' => 'Compagnon'],
                    ['lastName' => 'Gregoire'],
                    ['lastName' => null],
                ],
            ],
        ];

        /*
         * Test limit
         */

        yield 'sort-lastName-asc-limit' => [
            'payload' => [
                'start' => 1,
                'end' => 2,
                'filter' => [],
                'sort' => [
                    ['colId' => 'lastName', 'sort' => 'asc'],
                ],
            ],
            'expected' => [
                'total' => 3,
                'contacts' => [
                    ['lastName' => 'Gregoire'],
                ],
            ],
        ];

        /*
         * Test filter
         */

        yield 'filter-email-contains' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'email' => ['filter' => 'olivie.gregoire', 'filterType' => 'text', 'type' => 'contains'],
                ],
            ],
            'expected' => [
                'total' => 1,
                'contacts' => [
                    ['email' => 'olivie.gregoire@gmail.com'],
                ],
            ],
        ];

        yield 'filter-lastName-not-contains' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'lastName' => ['filter' => 'goire', 'filterType' => 'text', 'type' => 'notContains'],
                ],
            ],
            'expected' => [
                'total' => 1,
                'contacts' => [
                    ['lastName' => 'Compagnon'],
                ],
            ],
        ];

        yield 'filter-email-equals' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'email' => [
                        'filter' => 'olivie.gregoire@gmail.com',
                        'filterType' => 'text',
                        'type' => 'equals',
                    ],
                ],
            ],
            'expected' => [
                'total' => 1,
                'contacts' => [
                    ['email' => 'olivie.gregoire@gmail.com'],
                ],
            ],
        ];

        yield 'filter-email-not-equals' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'email' => [
                        'filter' => 'olivie.gregoire@gmail.com',
                        'filterType' => 'text',
                        'type' => 'notEqual',
                    ],
                ],
            ],
            'expected' => [
                'total' => 2,
                'contacts' => [
                    ['email' => 'a.compagnon@protonmail.com', 'opens' => 4, 'clicks' => 2],
                    ['email' => 'julien.dubois@exampleco.com', 'opens' => 0, 'clicks' => 0],
                ],
            ],
        ];

        yield 'filter-email-members' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'email' => ['filterType' => 'text', 'type' => 'member'],
                ],
            ],
            'expected' => [
                'total' => 2,
                'contacts' => [
                    ['email' => 'a.compagnon@protonmail.com', 'opens' => 4, 'clicks' => 2],
                ],
            ],
        ];

        yield 'filter-email-contacts' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'email' => ['filterType' => 'text', 'type' => 'contact'],
                ],
            ],
            'expected' => [
                'total' => 1,
                'contacts' => [
                    ['email' => 'olivie.gregoire@gmail.com', 'opens' => 3, 'clicks' => 3],
                ],
            ],
        ];

        yield 'filter-firstName-startWith-endWith-or-case-insensitive' => [
            'payload' => [
                'start' => 0,
                'end' => 100,
                'sort' => [],
                'filter' => [
                    'firstName' => [
                        'filterType' => 'text',
                        'operator' => 'OR',
                        'condition1' => [
                            'filter' => 'VIE',
                            'filterType' => 'text',
                            'type' => 'endsWith',
                        ],
                        'condition2' => [
                            'filter' => 'and',
                            'filterType' => 'text',
                            'type' => 'startsWith',
                        ],
                    ],
                ],
            ],
            'expected' => [
                'total' => 2,
                'contacts' => [
                    ['firstName' => 'Olivie'],
                    ['firstName' => 'André'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideReportSearch
     */
    public function testReportSearch(array $payload, array $expected)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/95b3f576-c643-45ba-9d5e-c9c44f65fab8/report');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/95b3f576-c643-45ba-9d5e-c9c44f65fab8/report/search',
            [],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            Json::encode($payload)
        );

        $this->assertResponseIsSuccessful();
        $this->assertApiResponse(Json::decode($client->getResponse()->getContent()), $expected);
    }

    public function testExport()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/95b3f576-c643-45ba-9d5e-c9c44f65fab8/report/export');

        // Should have published message
        $transport = static::getContainer()->get('messenger.transport.async_importing');
        $this->assertCount(1, $messages = $transport->get());

        /* @var ExportEmailingCampaignMessage $message */
        $this->assertInstanceOf(ExportEmailingCampaignMessage::class, $message = $messages[0]->getMessage());

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');

        $this->assertSame('en', $message->getLocale());
        $this->assertSame('titouan.galopin@citipo.com', $message->getEmail());
        $this->assertSame($campaign->getId(), $message->getCampaignId());
    }
}
