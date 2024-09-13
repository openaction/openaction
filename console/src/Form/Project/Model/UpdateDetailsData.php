<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UpdateDetailsData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Choice(['en', 'fr', 'pt_BR', 'nl', 'de', 'it', 'es'])]
    public string $locale = 'en';

    #[Assert\NotBlank]
    #[Assert\Choice(['global', 'local', 'thematic'])]
    public ?string $type = 'global';

    public ?string $areasIds = null;
    public ?string $tags = null;

    public static function createFromProject(Project $project): self
    {
        $self = new self();
        $self->name = $project->getName();
        $self->locale = $project->getWebsiteLocale();

        $self->type = 'global';
        if ($project->isLocal()) {
            $self->type = 'local';
        } elseif ($project->isThematic()) {
            $self->type = 'thematic';
        }

        $areas = [];
        foreach ($project->getAreas() as $area) {
            $id = $area->getId();
            $areas[$id] = ['id' => (string) $id, 'name' => $area->getName(), 'desc' => $area->getDescription()];
        }

        $self->areasIds = Json::encode($areas);

        $tags = [];
        foreach ($project->getTags() as $tag) {
            $id = $tag->getId();
            $tags[] = ['id' => (string) $id, 'name' => $tag->getName()];
        }

        $self->tags = Json::encode($tags);

        return $self;
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
