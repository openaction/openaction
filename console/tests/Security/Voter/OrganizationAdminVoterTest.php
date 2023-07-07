<?php

namespace App\Tests\Security\Voter;

use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\User;
use App\Platform\Permissions;
use App\Repository\OrganizationMemberRepository;
use App\Security\Voter\OrganizationAdminVoter;
use App\Tests\UnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizationAdminVoterTest extends UnitTestCase
{
    public function provideCases()
    {
        /*
         * Manage projects
         */

        // Anonymous
        yield 'anonymous cannot manage organization projects' => [
            'user' => null,
            'organization' => $this->createOrganization(1),
            'attribute' => Permissions::ORGANIZATION_PROJECT_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Outisde user
        yield 'outside user cannot manage organization projects' => [
            'user' => $this->createUser(1),
            'organization' => $this->createOrganization(1),
            'attribute' => Permissions::ORGANIZATION_PROJECT_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Organization member
        $user = $this->createUser(1);
        $organization = $this->createOrganization(1);
        $organization->getMembers()->add(new OrganizationMember($organization, $user, false));

        yield 'organization member cannot manage organization projects' => [
            'user' => $user,
            'organization' => $organization,
            'attribute' => Permissions::ORGANIZATION_PROJECT_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Organization admin
        $user = $this->createUser(1);
        $organization = $this->createOrganization(1, 'Orga');
        $organization->getMembers()->add(new OrganizationMember($organization, $user, true));

        yield 'organization admin can manage organization projects' => [
            'user' => $user,
            'organization' => $organization,
            'attribute' => Permissions::ORGANIZATION_PROJECT_MANAGE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        // Organization admin with offline subscription
        $user = $this->createUser(1);
        $organization = $this->createOrganization(1, 'Orga');
        $organization->getMembers()->add(new OrganizationMember($organization, $user, true));

        yield 'organization admin can manage offline organization projects' => [
            'user' => $user,
            'organization' => $organization,
            'attribute' => Permissions::ORGANIZATION_PROJECT_MANAGE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        /*
         * Manage team
         */

        // Anonymous
        yield 'anonymous cannot manage organization team' => [
            'user' => null,
            'organization' => $this->createOrganization(1),
            'attribute' => Permissions::ORGANIZATION_TEAM_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Outisde user
        yield 'outside user cannot manage organization team' => [
            'user' => $this->createUser(1),
            'organization' => $this->createOrganization(1),
            'attribute' => Permissions::ORGANIZATION_TEAM_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Organization member
        $user = $this->createUser(1);
        $organization = $this->createOrganization(1);
        $organization->getMembers()->add(new OrganizationMember($organization, $user, false));

        yield 'organization member cannot manage organization team' => [
            'user' => $user,
            'organization' => $organization,
            'attribute' => Permissions::ORGANIZATION_TEAM_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Organization admin
        $user = $this->createUser(1);
        $organization = $this->createOrganization(1, 'Orga');
        $organization->getMembers()->add(new OrganizationMember($organization, $user, true));

        yield 'organization admin can manage organization team' => [
            'user' => $user,
            'organization' => $organization,
            'attribute' => Permissions::ORGANIZATION_TEAM_MANAGE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        // Organization admin with offline subscription
        $user = $this->createUser(1);
        $organization = $this->createOrganization(1, 'Orga');
        $organization->getMembers()->add(new OrganizationMember($organization, $user, true));

        yield 'organization admin can manage offline organization team' => [
            'user' => $user,
            'organization' => $organization,
            'attribute' => Permissions::ORGANIZATION_TEAM_MANAGE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(?User $user, Organization $organization, string $attribute, int $expectedVote)
    {
        $repository = $this->createMock(OrganizationMemberRepository::class);
        $repository->method('findMember')->willReturnCallback(static function (User $user, Organization $orga) {
            foreach ($orga->getMembers() as $member) {
                if ($user->getId() === $member->getMember()->getId()) {
                    return $member;
                }
            }

            return null;
        });

        $voter = new OrganizationAdminVoter($repository);
        $token = $user ? new UsernamePasswordToken($user, 'memory') : new NullToken();

        $this->assertSame($expectedVote, $voter->vote($token, $organization, [$attribute]));
    }
}
