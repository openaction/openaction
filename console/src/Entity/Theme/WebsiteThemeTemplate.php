<?php

namespace App\Entity\Theme;

use App\Entity\Util;
use App\Repository\Theme\WebsiteThemeTemplateRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WebsiteThemeTemplateRepository::class)]
#[ORM\Table('website_themes_templates')]
#[ORM\UniqueConstraint(name: 'website_themes_templates_theme_name', columns: ['theme_id', 'name'])]
class WebsiteThemeTemplate
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: WebsiteTheme::class, inversedBy: 'templates')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private WebsiteTheme $theme;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    public function __construct(WebsiteTheme $theme, string $name, string $content, ?string $path = null)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->theme = $theme;
        $this->name = $name;
        $this->content = $content;
        $this->path = $path;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['theme'], $data['name'], $data['content'], $data['path'] ?? null);
        $self->createdAt = $data['createdAt'] ?? new \DateTime();

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function getTheme(): WebsiteTheme
    {
        return $this->theme;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}
