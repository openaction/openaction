<?php

namespace App\Form\Appearance\Model;

use App\Entity\Project;
use App\Entity\Theme\WebsiteTheme;
use Symfony\Component\Validator\Constraints as Assert;

class WebsiteThemeData
{
    #[Assert\NotBlank]
    public ?WebsiteTheme $theme = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 6)]
    public ?string $appearancePrimary = '002851';

    #[Assert\NotBlank]
    #[Assert\Length(max: 6)]
    public ?string $appearanceSecondary = '0F345C';

    #[Assert\NotBlank]
    #[Assert\Length(max: 6)]
    public ?string $appearanceThird = 'F34B50';

    #[Assert\NotBlank]
    public ?string $fontTitle = null;

    #[Assert\NotBlank]
    public ?string $fontText = null;

    #[Assert\NotBlank]
    public ?string $mainIntroPosition = null;

    public bool $mainIntroOverlay = true;

    public bool $animateElements = true;
    public bool $animateLinks = true;

    public static function createFromProject(Project $project): self
    {
        $self = new self();
        $self->theme = $project->getWebsiteTheme();
        $self->appearancePrimary = $project->getAppearancePrimary();
        $self->appearanceSecondary = $project->getAppearanceSecondary();
        $self->appearanceThird = $project->getAppearanceThird();
        $self->fontTitle = $project->getWebsiteFontTitle();
        $self->fontText = $project->getWebsiteFontText();
        $self->mainIntroPosition = $project->getWebsiteMainIntroPosition();
        $self->mainIntroOverlay = $project->hasWebsiteMainIntroOverlay();
        $self->animateElements = $project->isWebsiteAnimateElements();
        $self->animateLinks = $project->isWebsiteAnimateLinks();

        return $self;
    }
}
