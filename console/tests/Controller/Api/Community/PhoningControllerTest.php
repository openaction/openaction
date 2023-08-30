<?php

namespace App\Tests\Controller\Api\Community;

use App\Entity\Community\PhoningCampaignCall;
use App\Repository\Community\PhoningCampaignCallRepository;
use App\Tests\ApiTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class PhoningControllerTest extends ApiTestCase
{
    public function provideViewCampaign()
    {
        yield ['6zLC948rIUllTId3CQ9ius', 404]; // Draft
        yield ['4EoopwTWPEzYGj3tC4qdrm', 404]; // Finished

        // Active
        yield [
            'k15y7ErcYq4VUfah05iwy',
            200,
            [
                '_resource' => 'PhoningCampaign',
                'id' => 'k15y7ErcYq4VUfah05iwy',
                'name' => 'Active campaign',
                'form' => [
                    '_resource' => 'Form',
                    'title' => 'Another one to be answered on phoning campaign',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideViewCampaign
     */
    public function testViewCampaign(string $encodedUuid, int $expectedStatus, array $expectedContent = null)
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/phoning/'.$encodedUuid, $client)
            ->withApiToken(self::ACME_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame($expectedStatus);

        if ($expectedContent) {
            $this->assertApiResponse($result, $expectedContent);
        }
    }

    public function testResolveTarget()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('POST', '/api/community/area/phoning/k15y7ErcYq4VUfah05iwy/resolve-target', $client)
            ->withApiToken(self::ACME_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertApiResponse($result, [
            '_resource' => 'PhoningCampaignCall',
            'contact' => [
                '_resource' => 'Contact',
            ],
        ]);

        // A pending call should have been created
        /** @var PhoningCampaignCall $call */
        $call = static::getContainer()->get(PhoningCampaignCallRepository::class)->findOneByBase62Uid($result['id']);
        $this->assertSame(PhoningCampaignCall::STATUS_CALLING, $call->getStatus());
        $this->assertSame('jeanpaul@gmail.com', $call->getAuthor()->getEmail());
        $this->assertSame('Active campaign', $call->getTarget()->getCampaign()->getName());
    }

    public function testViewCall()
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        $result = $this->createApiRequest('GET', '/api/community/area/phoning/k15y7ErcYq4VUfah05iwy/call/5h0M9RUc5hl9gCQg3IYEJR', $client)
            ->withApiToken(self::ACME_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertApiResponse($result, [
            '_resource' => 'PhoningCampaignCall',
            'id' => '5h0M9RUc5hl9gCQg3IYEJR',
            'contact' => [
                '_resource' => 'Contact',
                'email' => 'jean.marting@gmail.com',
            ],
        ]);
    }

    public function provideSaveCall()
    {
        /** @var ApiTestCase $testCase */
        $testCase = $this;

        yield 'accepted' => [
            'status' => 'accepted',
            'answers' => [
                'Generic question' => 'first',
                'Email' => 'newEmail@gmail.com',
                'First name' => 'newFirstName',
                'Last name' => 'newLastName',
            ],
            'assertCallback' => static function (PhoningCampaignCall $call) use ($testCase) {
                $testCase->assertNotNull($call->getTarget()->getAnswer());
                $testCase->assertSame([
                    'Generic question' => 'first',
                    'Email' => 'newEmail@gmail.com',
                    'First name' => 'newFirstName',
                    'Last name' => 'newLastName',
                ], $call->getTarget()->getAnswer()->getAnswers());
                $testCase->assertSame('newemail@gmail.com', $call->getTarget()->getContact()->getEmail());
                $testCase->assertSame('newFirstName', $call->getTarget()->getContact()->getProfileFirstName());
                $testCase->assertSame('newLastName', $call->getTarget()->getContact()->getProfileLastName());
            },
        ];

        yield 'failed_invalid' => [
            'status' => 'failed_invalid',
            'answers' => [],
            'assertCallback' => static function (PhoningCampaignCall $call) use ($testCase) {
                $testCase->assertNull($call->getTarget()->getAnswer());
                $testCase->assertSame('jean.marting@gmail.com', $call->getTarget()->getContact()->getEmail());
                $testCase->assertSame('Jean', $call->getTarget()->getContact()->getProfileFirstName());
                $testCase->assertSame('Martin', $call->getTarget()->getContact()->getProfileLastName());
            },
        ];

        yield 'failed_no_answer' => [
            'status' => 'failed_no_answer',
            'answers' => [],
            'assertCallback' => static function (PhoningCampaignCall $call) use ($testCase) {
                $testCase->assertNull($call->getTarget()->getAnswer());
                $testCase->assertSame('jean.marting@gmail.com', $call->getTarget()->getContact()->getEmail());
                $testCase->assertSame('Jean', $call->getTarget()->getContact()->getProfileFirstName());
                $testCase->assertSame('Martin', $call->getTarget()->getContact()->getProfileLastName());
                $testCase->assertSame(['phoning-retry-active-campaign'], $call->getTarget()->getContact()->getMetadataTagsNames());
            },
        ];

        yield 'failed_no_call' => [
            'status' => 'failed_no_call',
            'answers' => [],
            'assertCallback' => static function (PhoningCampaignCall $call) use ($testCase) {
                $testCase->assertNull($call->getTarget()->getAnswer());
                $testCase->assertFalse($call->getTarget()->getContact()->hasSettingsReceiveCalls());
                $testCase->assertSame('jean.marting@gmail.com', $call->getTarget()->getContact()->getEmail());
                $testCase->assertSame('Jean', $call->getTarget()->getContact()->getProfileFirstName());
                $testCase->assertSame('Martin', $call->getTarget()->getContact()->getProfileLastName());
            },
        ];

        yield 'failed_unregister' => [
            'status' => 'failed_unregister',
            'answers' => [],
            'assertCallback' => static function (PhoningCampaignCall $call) use ($testCase) {
                $testCase->assertNull($call->getTarget()->getAnswer());
                $testCase->assertSame('jean.marting@gmail.com', $call->getTarget()->getContact()->getEmail());
                $testCase->assertSame('Jean', $call->getTarget()->getContact()->getProfileFirstName());
                $testCase->assertSame('Martin', $call->getTarget()->getContact()->getProfileLastName());
            },
        ];

        yield 'failed_call_later' => [
            'status' => 'failed_call_later',
            'answers' => [],
            'assertCallback' => static function (PhoningCampaignCall $call) use ($testCase) {
                $testCase->assertNull($call->getTarget()->getAnswer());
                $testCase->assertSame('jean.marting@gmail.com', $call->getTarget()->getContact()->getEmail());
                $testCase->assertSame('Jean', $call->getTarget()->getContact()->getProfileFirstName());
                $testCase->assertSame('Martin', $call->getTarget()->getContact()->getProfileLastName());
                $testCase->assertSame(['phoning-retry-active-campaign'], $call->getTarget()->getContact()->getMetadataTagsNames());
            },
        ];
    }

    /**
     * @dataProvider provideSaveCall
     */
    public function testSaveCall(string $status, array $answers, callable $assertCallback)
    {
        $client = self::createClient();
        $token = $this->authenticateAsMember($client);

        // The call should be pending and not answered
        /** @var PhoningCampaignCall $call */
        $call = static::getContainer()->get(PhoningCampaignCallRepository::class)->findOneByBase62Uid('5h0M9RUc5hl9gCQg3IYEJR');
        $this->assertSame(PhoningCampaignCall::STATUS_CALLING, $call->getStatus());
        $this->assertNull($call->getTarget()->getAnswer());
        $this->assertSame('jean.marting@gmail.com', $call->getTarget()->getContact()->getEmail());
        $this->assertSame('Jean', $call->getTarget()->getContact()->getProfileFirstName());
        $this->assertSame('Martin', $call->getTarget()->getContact()->getProfileLastName());

        $this->createApiRequest('POST', '/api/community/area/phoning/k15y7ErcYq4VUfah05iwy/call/5h0M9RUc5hl9gCQg3IYEJR/save', $client)
            ->withApiToken(self::ACME_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->withContent(Json::encode(['status' => $status, 'answers' => $answers]))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        // The call should have been updated
        /** @var PhoningCampaignCall $call */
        $call = static::getContainer()->get(PhoningCampaignCallRepository::class)->findOneByBase62Uid('5h0M9RUc5hl9gCQg3IYEJR');
        $this->assertSame($status, $call->getStatus());

        static::getContainer()->get(EntityManagerInterface::class)->refresh($call->getTarget()->getContact());
        $assertCallback($call);
    }

    private function authenticateAsMember(KernelBrowser $client): array
    {
        return $this->apiRequest($client, 'POST', '/api/community/members/login', self::ACME_TOKEN, 200, Json::encode([
            'email' => 'jeanpaul@gmail.com',
            'password' => 'password',
        ]));
    }
}
