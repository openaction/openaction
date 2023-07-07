<?php

namespace App\Tests\Security\Voter;

use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\Project;
use App\Entity\User;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Platform\Plans;
use App\Repository\OrganizationMemberRepository;
use App\Security\Voter\OrganizationMemberVoter;
use App\Tests\UnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizationMemberVoterTest extends UnitTestCase
{
    public function provideCases()
    {
        /*
         * Anonymous or non-member
         */

        // Anonymous => denied
        yield 'anonymous' => [
            'user' => null,
            'project' => $this->createProject(1),
            'attribute' => Permissions::WEBSITE_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Not a member of the organization => denied
        yield 'non-member' => [
            'user' => $this->createUser(1),
            'project' => $this->createProject(1),
            'attribute' => Permissions::WEBSITE_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        /*
         * Admin
         */

        $user = $this->createUser(1);

        $orga = $this->createOrganization(1, 'Orga', Plans::ESSENTIAL);
        $orga->getMembers()->add(new OrganizationMember($orga, $user, true));

        $project = $this->createProject(1, 'Project', $orga);
        $project->updateModules(Features::allModules(), Features::allTools());

        yield 'admin-module' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        yield 'admin-tool' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_PAGES_MANAGE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        yield 'admin-tool-outside-plan' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        $project = $this->createProject(1, 'Project', $orga);
        $project->updateModules([], []);

        yield 'admin-disabled-module' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        yield 'admin-disabled-tool' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_PAGES_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        /*
         * Members
         */

        $user = $this->createUser(1);

        $orga = $this->createOrganization(1, 'Orga', Plans::ESSENTIAL);

        $project = $this->createProject(1, 'Project', $orga);
        $project->updateModules(Features::allModules(), Features::allTools());

        $orga->getMembers()->add(new OrganizationMember($orga, $user, false, [
            $project->getUuid()->toRfc4122() => [
                Permissions::WEBSITE_PAGES_MANAGE => true,
                Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS => true,
            ],
        ]));

        yield 'member-module' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        yield 'member-tool' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_PAGES_MANAGE,
            'expected_vote' => Voter::ACCESS_GRANTED,
        ];

        yield 'member-tool-outside-plan' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        // Member who doesn't have any module permission => denied
        yield 'member-no-module-permission' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::COMMUNITY_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        $project = $this->createProject(1, 'Project', $orga);
        $project->updateModules([], []);

        yield 'member-disabled-module' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_SEE_MODULE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];

        yield 'member-disabled-tool' => [
            'user' => $user,
            'project' => $project,
            'attribute' => Permissions::WEBSITE_PAGES_MANAGE,
            'expected_vote' => Voter::ACCESS_DENIED,
        ];
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(?User $user, Project $project, string $attribute, int $expectedVote)
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

        $voter = new OrganizationMemberVoter($repository);
        $token = $user ? new UsernamePasswordToken($user, 'memory') : new NullToken();

        $this->assertSame($expectedVote, $voter->vote($token, $project, [$attribute]));
    }
}
