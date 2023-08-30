<?php

namespace App\Tests\Controller\Api\Community;

use App\Entity\Website\Form;
use App\Entity\Website\FormAnswer;
use App\Repository\Website\FormAnswerRepository;
use App\Repository\Website\FormRepository;
use App\Tests\ApiTestCase;
use App\Tests\Controller\Api\Website\FormControllerTest;
use App\Util\Json;
use App\Util\Uid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AreaControllerTest extends ApiTestCase
{
    public function testListPages()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/pages', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertCount(1, $result['data']);

        // Test content is not included in the payload
        $this->assertArrayNotHasKey('content', $result['data'][0]);

        // Test the payload
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Page',
                    '_links' => [
                        'self' => 'http://localhost/api/community/area/pages/14nA0lolffAnALGPRxdlrN',
                    ],
                    'id' => '14nA0lolffAnALGPRxdlrN',
                    'title' => 'Only for members',
                    'slug' => 'only-for-members',
                    'description' => 'Only for members',
                    'image' => null,
                    'sharer' => null,
                    'categories' => [
                        'data' => [
                            [
                                '_resource' => 'PageCategory',
                                '_links' => [
                                    'self' => 'http://localhost/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ',
                                ],
                                'id' => '7hIQY74GJcZWKsJxafwbHZ',
                                'name' => 'Category 2',
                                'slug' => 'category-2',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testListPagesCategoryFilters()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/pages?category=2j7qdd4EDE0CJVOw0WCWAp', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertCount(0, $result['data']);
    }

    public function testViewPage()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/pages/14nA0lolffAnALGPRxdlrN', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        // Test the payload, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Page',
            '_links' => [
                'self' => 'http://localhost/api/community/area/pages/14nA0lolffAnALGPRxdlrN',
            ],
            'id' => '14nA0lolffAnALGPRxdlrN',
            'title' => 'Only for members',
            'slug' => 'only-for-members',
            'description' => 'Only for members',
            'content' => '',
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [
                    [
                        '_resource' => 'PageCategory',
                        '_links' => [
                            'self' => 'http://localhost/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ',
                        ],
                        'id' => '7hIQY74GJcZWKsJxafwbHZ',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                    ],
                ],
            ],
        ]);
    }

    public function testListPosts()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/posts', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(1, $result['data']);

        // Test content is not included in the payload
        $this->assertArrayNotHasKey('content', $result['data'][0]);

        // Test the payload
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Post',
                    '_links' => [
                        'self' => 'http://localhost/api/community/area/posts/14nA0lolffAnALGPRxdlrN',
                    ],
                    'id' => '14nA0lolffAnALGPRxdlrN',
                    'title' => 'Only for members',
                    'quote' => null,
                    'slug' => 'only-for-members',
                    'description' => null,
                    'video' => null,
                    'image' => null,
                    'sharer' => null,
                    'categories' => [
                        'data' => [
                            [
                                '_resource' => 'PostCategory',
                                '_links' => [
                                    'self' => 'http://localhost/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1',
                                ],
                                'id' => '1GmkaorS3YSezgfKGrZel1',
                                'name' => 'Category 2',
                                'slug' => 'category-2',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testListPostsCategoryFilters()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/posts?category=6Xtiq0UNncsemt50yvqsoj', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(0, $result['data']);
    }

    public function testViewPost()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/posts/14nA0lolffAnALGPRxdlrN', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        // Test the payload, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Post',
            '_links' => [
                'self' => 'http://localhost/api/community/area/posts/14nA0lolffAnALGPRxdlrN',
            ],
            'id' => '14nA0lolffAnALGPRxdlrN',
            'title' => 'Only for members',
            'quote' => null,
            'slug' => 'only-for-members',
            'description' => null,
            'content' => '',
            'video' => null,
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [
                    [
                        '_resource' => 'PostCategory',
                        '_links' => [
                            'self' => 'http://localhost/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1',
                        ],
                        'id' => '1GmkaorS3YSezgfKGrZel1',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                    ],
                ],
            ],
        ]);
    }

    public function testListEvents()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/events', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(1, $result['data']);

        // Test the payload
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Event',
                    '_links' => [
                        'self' => 'http://localhost/api/community/area/events/14nA0lolffAnALGPRxdlrN',
                    ],
                    'id' => '14nA0lolffAnALGPRxdlrN',
                    'title' => 'Only for members',
                    'slug' => 'only-for-members',
                    'content' => '',
                    'url' => null,
                    'buttonText' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'address' => null,
                    'image' => null,
                    'sharer' => null,
                    'categories' => [
                        'data' => [
                            [
                                '_resource' => 'EventCategory',
                                '_links' => [
                                    'self' => 'http://localhost/api/website/events-categories/1RBufbuXcErYt5XplZHhc',
                                ],
                                'id' => '1RBufbuXcErYt5XplZHhc',
                                'name' => 'Category 2',
                                'slug' => 'category-2',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testListEventsCategoryFilters()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/events?category=1r1BjY3gmpXo6e4KH7kMNU', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(0, $result['data']);
    }

    public function testViewEvent()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/events/14nA0lolffAnALGPRxdlrN', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        // Test the payload, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Event',
            '_links' => [
                'self' => 'http://localhost/api/community/area/events/14nA0lolffAnALGPRxdlrN',
            ],
            'id' => '14nA0lolffAnALGPRxdlrN',
            'title' => 'Only for members',
            'slug' => 'only-for-members',
            'content' => '',
            'url' => null,
            'buttonText' => null,
            'latitude' => null,
            'longitude' => null,
            'address' => null,
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [
                    [
                        '_resource' => 'EventCategory',
                        '_links' => [
                            'self' => 'http://localhost/api/website/events-categories/1RBufbuXcErYt5XplZHhc',
                        ],
                        'id' => '1RBufbuXcErYt5XplZHhc',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                    ],
                ],
            ],
        ]);
    }

    public function testListForms()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/forms', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(1, $result['data']);

        // Test the payload
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Form',
                    '_links' => [
                        'self' => 'http://localhost/api/community/area/forms/5ABVt8CfIxY5GrexUynjW4',
                        'answer' => 'http://localhost/api/website/forms/5ABVt8CfIxY5GrexUynjW4/answer',
                    ],
                    'id' => '5ABVt8CfIxY5GrexUynjW4',
                    'title' => 'Member file upload form',
                    'slug' => 'member-file-upload-form',
                    'description' => null,
                    'proposeNewsletter' => false,
                    'phoningCampaignId' => null,
                    'blocks' => [
                        'data' => [
                            [
                                '_resource' => 'FormBlock',
                                'type' => 'file',
                                'content' => 'File upload',
                                'field' => true,
                                'required' => true,
                                'config' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testViewForm()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/forms/5ABVt8CfIxY5GrexUynjW4', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        // Test the payload, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Form',
            '_links' => [
                'self' => 'http://localhost/api/community/area/forms/5ABVt8CfIxY5GrexUynjW4',
                'answer' => 'http://localhost/api/website/forms/5ABVt8CfIxY5GrexUynjW4/answer',
            ],
            'id' => '5ABVt8CfIxY5GrexUynjW4',
            'title' => 'Member file upload form',
            'slug' => 'member-file-upload-form',
            'description' => null,
            'proposeNewsletter' => false,
            'phoningCampaignId' => null,
            'blocks' => [
                'data' => [
                    [
                        '_resource' => 'FormBlock',
                        'type' => 'file',
                        'content' => 'File upload',
                        'field' => true,
                        'required' => true,
                        'config' => [],
                    ],
                ],
            ],
        ]);
    }

    /**
     * This test only tests the contact associated to the answer is the logged in member.
     * For the full testing of mapping, emails and integrations,
     * {@see FormControllerTest::testAnswerNewContactFullMapping}.
     */
    public function testAnswerLinksContact()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $this->createApiRequest('POST', '/api/website/forms/5ABVt8CfIxY5GrexUynjW4/answer', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->withContent(Json::encode([
                'fields' => [
                    'https://app.uploadcare.com/file',
                ],
            ]))
            ->send()
        ;

        $this->assertResponseStatusCodeSame(201);

        /*
         * Check the answer
         */

        /** @var Form $form */
        $form = static::getContainer()->get(FormRepository::class)->findOneBy(['uuid' => Uid::fromBase62('5ABVt8CfIxY5GrexUynjW4')]);
        $this->assertInstanceOf(Form::class, $form);

        /** @var FormAnswer $answer */
        $answer = static::getContainer()->get(FormAnswerRepository::class)->findOneBy(['form' => $form]);
        $this->assertInstanceOf(FormAnswer::class, $answer);
        $this->assertSame(['File upload' => 'https://app.uploadcare.com/file'], $answer->getAnswers());

        // Check the logged in member was properly linked
        $this->assertNotNull($answer->getContact());
        $this->assertSame('julien.dubois@exampleco.com', $answer->getContact()->getEmail());
    }

    public function provideRestrictedUrls()
    {
        $urls = [
            '/api/community/area/pages',
            '/api/community/area/pages/14nA0lolffAnALGPRxdlrN',
            '/api/community/area/posts',
            '/api/community/area/posts/14nA0lolffAnALGPRxdlrN',
            '/api/community/area/events',
            '/api/community/area/events/14nA0lolffAnALGPRxdlrN',
            '/api/community/area/forms',
            '/api/community/area/forms/14nA0lolffAnALGPRxdlrN',
        ];

        foreach ($urls as $url) {
            yield $url.' - empty token' => [$url, ''];
            yield $url.' - non-JSON token' => [$url, 'invalid'];
            yield $url.' - invalid token' => [$url, Json::encode([
                '_resource' => 'AuthorizationToken',
                'firstName' => 'Titouan',
                'lastName' => 'Galopin',
                'nonce' => 'nonce',
                'encrypted' => 'encrypted',
            ])];
        }
    }

    /**
     * @dataProvider provideRestrictedUrls
     */
    public function testRestrictedUrls(string $url, string $token)
    {
        $this->createApiRequest('GET', $url, self::createClient())
            ->withApiToken(self::CITIPO_TOKEN)
            ->withAuthToken($token)
            ->send()
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    private function authenticateAsMember(KernelBrowser $client): array
    {
        $result = $this->createApiRequest('POST', '/api/community/members/login', $client)
            ->withApiToken(self::DEFAULT_TOKEN)
            ->withContent(Json::encode([
                'email' => 'julien.dubois@exampleco.com',
                'password' => 'password',
            ]))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        return $result;
    }
}
