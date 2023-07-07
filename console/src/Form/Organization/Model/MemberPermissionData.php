<?php

namespace App\Form\Organization\Model;

use App\Entity\OrganizationMember;
use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MemberPermissionData
{
    public bool $isAdmin = false;

    public ?string $labels = '';

    #[Assert\Json]
    public ?string $projectsPermissions = '{}';

    public static function createFromMember(OrganizationMember $member): self
    {
        $self = new self();
        $self->isAdmin = $member->isAdmin();
        $self->labels = implode('|', $member->getLabels());
        $self->projectsPermissions = Json::encode($member->getRawProjectsPermissions());

        return $self;
    }

    public function parseProjectPermissionsArray(): array
    {
        try {
            return Json::decode($this->projectsPermissions) ?: [];
        } catch (\JsonException) {
            return [];
        }
    }

    public function parseLabelsArray(): array
    {
        return array_filter(array_unique(array_map('trim', explode('|', (string) $this->labels))));
    }

    #[Assert\Callback(groups: ['permission'])]
    public function validate(ExecutionContextInterface $context)
    {
        $data = $this->parseProjectPermissionsArray();
        $error = true;
        foreach ($data as $permissions) {
            foreach ($permissions as $value) {
                if ($value) {
                    $error = false;
                    break;
                }
            }
        }
        if ($error) {
            $context
                ->buildViolation('console.organization.team.empty_permissions')
                ->atPath('projectsPermissions')
                ->addViolation()
            ;
        }
    }
}
