<?php

namespace App\Form\Organization\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MemberInviteData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    #[Assert\Email]
    public ?string $email = null;

    public ?bool $isAdmin = false;

    #[Assert\NotBlank]
    #[Assert\Choice(['fr', 'en', 'de'])]
    public ?string $locale = null;

    #[Assert\Json]
    public ?string $projectsPermissions = '';

    #[Assert\Json]
    public ?string $projectsPermissionsCategories = '{}';

    public function parseProjectPermissions(): array
    {
        try {
            return Json::decode($this->projectsPermissions) ?: [];
        } catch (\JsonException) {
            return [];
        }
    }

    public function parseProjectPermissionsCategories(): array
    {
        try {
            return Json::decode($this->projectsPermissionsCategories) ?: [];
        } catch (\JsonException) {
            return [];
        }
    }

    #[Assert\Callback(groups: ['permission'])]
    public function validate(ExecutionContextInterface $context)
    {
        $data = $this->parseProjectPermissions();
        $error = true;
        foreach ($data as $projectId => $permissions) {
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
