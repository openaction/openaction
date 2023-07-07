<?php

namespace App\Form\Organization\Model;

use App\Platform\Features;
use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateProjectData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Choice(['global', 'local', 'thematic'])]
    public ?string $type = 'global';

    public ?string $areasIds = null;
    public ?string $tags = null;

    #[Assert\All([new Assert\Choice(['callback' => [Features::class, 'allModules']])])]
    #[Assert\NotBlank]
    public ?array $modules;

    #[Assert\All([new Assert\Choice(['callback' => [Features::class, 'allTools']])])]
    #[Assert\NotBlank]
    public ?array $tools = [];

    public function __construct(?array $modules, ?array $tools)
    {
        $this->modules = $modules;
        $this->tools = $tools;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if ('local' === $this->type && !$this->parseAreasIds()) {
            $context->buildViolation('console.organization.manage_project.missing_area')
                ->atPath('type')
                ->addViolation()
            ;
        }
        if ('thematic' === $this->type && !$this->parseTags()) {
            $context->buildViolation('console.organization.manage_project.missing_tag')
                ->atPath('type')
                ->addViolation()
            ;
        }
    }

    public function parseAreasIds(): array
    {
        if (!$this->areasIds) {
            return [];
        }

        try {
            return array_keys(Json::decode($this->areasIds));
        } catch (\Throwable) {
            return [];
        }
    }

    public function parseTags(): array
    {
        if (!$this->tags) {
            return [];
        }

        try {
            return array_column(Json::decode($this->tags), 'id');
        } catch (\Throwable) {
            return [];
        }
    }
}
