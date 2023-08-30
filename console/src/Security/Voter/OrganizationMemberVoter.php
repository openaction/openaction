<?php

namespace App\Security\Voter;

use App\Entity\Integration\TelegramAppAuthorization;
use App\Entity\Project;
use App\Entity\User;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\OrganizationMemberRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizationMemberVoter extends Voter
{
    public function __construct(private OrganizationMemberRepository $memberRepository)
    {
    }

    protected function supports($permission, $entity): bool
    {
        return $entity instanceof Project
            && in_array($permission, Permissions::allForProjects(), true);
    }

    /**
     * @param Project $project
     */
    protected function voteOnAttribute(string $permission, $project, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if ($user instanceof TelegramAppAuthorization) {
            $user = $user->getMember();
        }

        if (!$user instanceof User) {
            return false;
        }

        if (!$member = $this->memberRepository->findMember($user, $project->getOrganization())) {
            return false;
        }

        if ($this->isModulePermission($permission)) {
            return $this->voteOnModulePermission($permission, $project, $token);
        }

        if (!$this->isToolEnabled($project, $permission)) {
            return false;
        }

        if ($member->isAdmin()) {
            return true;
        }

        return $member->getProjectsPermissions()->hasPermission($project->getUuid(), $permission);
    }

    private function isModulePermission(string $permission): bool
    {
        return in_array($permission, [
            Permissions::WEBSITE_SEE_MODULE,
            Permissions::COMMUNITY_SEE_MODULE,
        ], true);
    }

    /*
     * Vote on whether a user can see a given module or not.
     * A user can see a module if the module is enabled and they have access to at least one tool.
     */
    private function voteOnModulePermission(string $permission, Project $project, TokenInterface $token): bool
    {
        switch ($permission) {
            case Permissions::WEBSITE_SEE_MODULE:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE) && (
                    $this->voteOnAttribute(Permissions::WEBSITE_PAGES_MANAGE, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_PAGES_MANAGE_CATEGORIES, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_POSTS_MANAGE_PUBLISHED, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_POSTS_MANAGE_CATEGORIES, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_DOCUMENTS_MANAGE, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_EVENTS_MANAGE_PUBLISHED, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_FORMS_MANAGE, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $project, $token)
                );

            case Permissions::COMMUNITY_SEE_MODULE:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY) && (
                    $this->voteOnAttribute(Permissions::COMMUNITY_CONTACTS_VIEW, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_CONTACTS_UPDATE, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_CONTACTS_DELETE, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_CONTACTS_TAG_ADD, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_EMAIL_SEND, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_TEXTING_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_TEXTING_SEND, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_PHONING_MANAGE_ACTIVE, $project, $token)
                    || $this->voteOnAttribute(Permissions::COMMUNITY_ACCESS_STATS, $project, $token)
                );
        }

        return false;
    }

    private function isToolEnabled(Project $project, string $permission): bool
    {
        switch ($permission) {
            case Permissions::PROJECT_CONFIG_APPEARANCE:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE);

            case Permissions::PROJECT_DEVELOPER_THEME:
            case Permissions::PROJECT_DEVELOPER_REDIRECTIONS:
                return true;

            case Permissions::PROJECT_CONFIG_SOCIALS:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isFeatureInPlan(Features::FEATURE_WEBSITE_SOCIAL_SHARERS);

            case Permissions::PROJECT_DEVELOPER_ACCESS:
                return $project->isFeatureInPlan(Features::FEATURE_API);

            case Permissions::WEBSITE_ACCESS_STATS:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isFeatureInPlan(Features::FEATURE_WEBSITE_STATS);

            case Permissions::WEBSITE_PAGES_MANAGE:
            case Permissions::WEBSITE_PAGES_MANAGE_CATEGORIES:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_PAGES)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_PAGES);

            case Permissions::WEBSITE_POSTS_MANAGE_DRAFTS:
            case Permissions::WEBSITE_POSTS_MANAGE_PUBLISHED:
            case Permissions::WEBSITE_POSTS_PUBLISH:
            case Permissions::WEBSITE_POSTS_MANAGE_CATEGORIES:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_POSTS)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_POSTS);

            case Permissions::WEBSITE_DOCUMENTS_MANAGE:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_DOCUMENTS)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_DOCUMENTS);

            case Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS:
            case Permissions::WEBSITE_EVENTS_MANAGE_PUBLISHED:
            case Permissions::WEBSITE_EVENTS_PUBLISH:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_EVENTS)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_EVENTS);

            case Permissions::WEBSITE_FORMS_MANAGE:
            case Permissions::WEBSITE_FORMS_ACCESS_RESULTS:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_FORMS)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_FORMS);

            case Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS:
            case Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED:
            case Permissions::WEBSITE_TROMBINOSCOPE_PUBLISH:
            case Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_TROMBINOSCOPE)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_TROMBINOSCOPE);

            case Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS:
            case Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED:
            case Permissions::WEBSITE_MANIFESTO_PUBLISH:
                return $project->isModuleEnabled(Features::MODULE_WEBSITE)
                    && $project->isToolEnabled(Features::TOOL_WEBSITE_MANIFESTO)
                    && $project->isFeatureInPlan(Features::TOOL_WEBSITE_MANIFESTO);

            case Permissions::COMMUNITY_CONTACTS_VIEW:
            case Permissions::COMMUNITY_CONTACTS_UPDATE:
            case Permissions::COMMUNITY_CONTACTS_DELETE:
            case Permissions::COMMUNITY_CONTACTS_TAG_ADD:
            case Permissions::COMMUNITY_ACCESS_STATS:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_CONTACTS)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_CONTACTS);

            case Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS:
            case Permissions::COMMUNITY_EMAIL_SEND:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_EMAILING)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_EMAILING);

            case Permissions::COMMUNITY_EMAIL_STATS:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_EMAILING)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_EMAILING)
                    && $project->isFeatureInPlan(Features::FEATURE_COMMUNITY_EMAILING_STATS);

            case Permissions::COMMUNITY_TEXTING_MANAGE_DRAFTS:
            case Permissions::COMMUNITY_TEXTING_SEND:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_TEXTING)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_TEXTING);

            case Permissions::COMMUNITY_TEXTING_STATS:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_TEXTING)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_TEXTING)
                    && $project->isFeatureInPlan(Features::FEATURE_COMMUNITY_TEXTING_STATS);

            case Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS:
            case Permissions::COMMUNITY_PHONING_MANAGE_ACTIVE:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_PHONING)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_PHONING);

            case Permissions::COMMUNITY_PHONING_STATS:
                return $project->isModuleEnabled(Features::MODULE_COMMUNITY)
                    && $project->isToolEnabled(Features::TOOL_COMMUNITY_PHONING)
                    && $project->isFeatureInPlan(Features::TOOL_COMMUNITY_PHONING)
                    && $project->isFeatureInPlan(Features::FEATURE_COMMUNITY_PHONING_STATS);
        }

        return false;
    }
}
