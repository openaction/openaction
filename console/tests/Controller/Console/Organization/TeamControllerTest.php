<?php

namespace App\Tests\Controller\Console\Organization;

use App\Entity\OrganizationMember;
use App\Entity\Registration;
use App\Repository\OrganizationMemberRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RegistrationRepository;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TeamControllerTest extends WebTestCase
{
    private const MEMBER_ID = '593afb16-b41a-4f52-9a13-c11aedcd6b27';

    public function testListMembers()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.world-box.administrator'));
    }

    public function provideRemoveMember()
    {
        yield [
            'arianneverreau@example.com',
            self::ORGA_CITIPO_UUID,
            '2c60ea80-87b4-479a-b78a-20a36ba3adf1',
        ];
    }

    /**
     * @dataProvider provideRemoveMember
     */
    public function testRemoveMember(string $email, string $orgaUuid, string $uuid)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $this->createMember($email, $orgaUuid, $uuid);

        $crawler = $client->request('GET', '/console/organization/'.$orgaUuid.'/team');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-box'));
        $this->assertCount(1, $crawler->filter('.world-box.collaborator'));

        $client->click($crawler->filter('.world-box.collaborator')->link());
        $this->assertResponseIsSuccessful();

        $client->clickLink('Remove');

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('.world-box'));
        $this->assertCount(0, $crawler->filter('.world-box.collaborator'));
    }

    public function provideInviteNewMember()
    {
        yield 'registration_admin' => [
            'user.member@citipo.email',
            1,
            true,
            [],
        ];

        yield 'registration_collaborator' => [
            'user.member@citipo.email',
            1,
            false,
            [
                self::ORGA_CITIPO_UUID => [
                    'website_see_module' => true,
                ],
                '682746ea-3e2f-4e5b-983b-6548258a2033' => [
                    'website_pages_manage' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInviteNewMember
     */
    public function testInviteNewMember(string $email, int $countResult, bool $isAdmin, array $permissions)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Invite a new collaborator');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Send the invite')->form(), [
            'member_invite[email]' => $email,
            'member_invite[isAdmin]' => $isAdmin,
            'member_invite[projectsPermissions]' => Json::encode($permissions),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();

        /** @var Registration $registration */
        $registration = static::getContainer()->get(RegistrationRepository::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertEquals($isAdmin, $registration->isAdmin());
        $this->assertEquals($permissions, $registration->getProjectsPermissions());

        $this->assertResponseIsSuccessful();
        $this->assertCount($countResult, $crawler->filter('.world-box'));
    }

    public function provideInviteExistingMember()
    {
        yield 'organization_member_admin' => [
            'arianneverreau@example.com',
            2,
            true,
            [],
        ];

        yield 'organization_member_collaborator' => [
            'arianneverreau@example.com',
            2,
            false,
            [
                'e816bcc6-0568-46d1-b0c5-917ce4810a87' => [
                    'website_posts_manage_drafts' => true,
                ],
                '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                    'website_posts_manage_published' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInviteExistingMember
     */
    public function testInviteExistingMember(string $email, int $countResult, bool $isAdmin, array $permissions)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Invite a new collaborator');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Send the invite')->form(), [
            'member_invite[email]' => $email,
            'member_invite[isAdmin]' => $isAdmin,
            'member_invite[projectsPermissions]' => Json::encode($permissions),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();

        /** @var OrganizationMember $member */
        $member = static::getContainer()->get(OrganizationMemberRepository::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertSame($isAdmin, $member->isAdmin());
        $this->assertSame($permissions, $member->getRawProjectsPermissions());
        $this->assertNotEmpty($member->getCrmTenantToken());

        $this->assertResponseIsSuccessful();
        $this->assertCount($countResult, $crawler->filter('.world-box'));
    }

    public function provideInviteMemberInvalidData()
    {
        yield 'invalid_email_admin' => ['xxx', true, '[]'];

        yield 'invalid_email' => ['xxx', false, Json::encode([
            '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                'website_posts_manage_drafts' => true,
            ],
        ])];

        yield 'invalid_permissions_json' => ['admin@citipo.email', false, null];

        yield 'invalid_permissions' => ['admin@citipo.email', false, Json::encode([
            '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                'website_posts_manage_drafts' => false,
            ],
        ])];
    }

    /**
     * @dataProvider provideInviteMemberInvalidData
     */
    public function testInviteMemberInvalidData(string $email, bool $isAdmin, $permissions)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team/invite/member');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Send the invite')->form(), [
            'member_invite[email]' => $email,
            'member_invite[isAdmin]' => $isAdmin,
            'member_invite[projectsPermissions]' => $permissions,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.form-error-message');
    }

    public function provideMemberPermissions()
    {
        yield [
            'arianneverreau@example.com',
            [
                '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                    'website_posts_manage_drafts' => true,
                    'website_documents_manage' => true,
                ],
            ],
            true,
            2,
        ];

        yield [
            'arianneverreau@example.com',
            [
                '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                    'website_posts_manage_published' => false,
                    'website_documents_manage' => true,
                ],
            ],
            false,
            1,
        ];
    }

    /**
     * @dataProvider provideMemberPermissions
     */
    public function testEditPermissionsMember(string $email, array $permissions, bool $expected, int $countCheck)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $member = $this->createMember(
            $email,
            self::ORGA_CITIPO_UUID,
            self::MEMBER_ID,
            false,
            $permissions
        );

        $projectId = key($permissions);
        $permission = key($permissions[$projectId]);

        $has = $permissions[$projectId][$permission];

        $this->assertSame($expected, $member->getProjectsPermissions()->hasPermission($projectId, $permission));
        $this->assertSame([], $member->getLabels());

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team');
        $this->assertResponseIsSuccessful();

        $crawler = $client->click($crawler->filter('.world-box.collaborator')->link());
        $this->assertResponseIsSuccessful();
        $this->assertCount($countCheck, $crawler->filter('input:checked'));

        $merge = [$permission => !$has] + $permissions[$projectId];
        $newPermissions = [$projectId => $merge];

        $client->submit($crawler->selectButton('Save')->form(), [
            'member_permission[projectsPermissions]' => Json::encode($newPermissions),
            'member_permission[labels]' => implode('|', ['referent', 'ile-de-france']),
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var OrganizationMember */
        $member = static::getContainer()->get(OrganizationMemberRepository::class)->findOneBy([
            'uuid' => self::MEMBER_ID,
        ]);

        $this->assertSame(!$has, $member->getProjectsPermissions()->hasPermission($projectId, $permission));
        $this->assertSame(['referent', 'ile-de-france'], $member->getLabels());
        $this->assertNotEmpty($member->getCrmTenantToken());
    }

    public function testEditPermissionsRevokeAdmin()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $member = $this->createMember('arianneverreau@example.com', self::ORGA_CITIPO_UUID, self::MEMBER_ID, true);
        $this->assertTrue($member->isAdmin());

        $permissions = [
            '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                'website_documents_manage' => true,
            ],
        ];

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team/'.$member->getUuid().'/permissions');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Save')->form(), [
            'member_permission[isAdmin]' => false,
            'member_permission[projectsPermissions]' => Json::encode($permissions),
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $member = static::getContainer()->get(OrganizationMemberRepository::class)->findOneBy([
            'uuid' => self::MEMBER_ID,
        ]);

        $this->assertFalse($member->isAdmin());
        $this->assertEquals($permissions, $member->getRawProjectsPermissions());
        $this->assertNotEmpty($member->getCrmTenantToken());
    }

    public function provideEditMemberInvalidData()
    {
        yield 'invalid_permissions_json' => [false, null];

        yield 'invalid_permission_empty' => [false, Json::encode([
            '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                'website_posts_manage_drafts' => false,
            ],
        ])];
    }

    /**
     * @dataProvider provideEditMemberInvalidData
     */
    public function testEditMemberInvalidData(bool $isAdmin, $permissions)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $member = $this->createMember('arianneverreau@example.com', self::ORGA_CITIPO_UUID, self::MEMBER_ID);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team/'.$member->getUuid().'/permissions');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Save')->form(), [
            'member_permission[isAdmin]' => $isAdmin,
            'member_permission[projectsPermissions]' => $permissions,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.form-error-message');
    }

    public function testListPending()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team/pending');
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.world-box.administrator'));
        $this->assertCount(1, $crawler->filter('.world-box.collaborator'));
        $this->assertSelectorExists('.world-box.collaborator:contains("Citipo, Île-de-France")');
    }

    public function testDeletePending()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/team/pending');
        $this->assertResponseIsSuccessful();

        $client->click($crawler->filter('.world-box.collaborator')->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.world-box.administrator'));
        $this->assertCount(0, $crawler->filter('.world-box.collaborator'));
    }

    private function createMember(string $email, string $orga, ?string $uuid, bool $isAdmin = false, array $perms = [])
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $member = OrganizationMember::createFixture([
            'uuid' => $uuid,
            'user' => static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]),
            'orga' => static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => $orga]),
            'isAdmin' => $isAdmin,
            'permissions' => $perms,
        ]);

        $em->persist($member);
        $em->flush();

        return $member;
    }
}
