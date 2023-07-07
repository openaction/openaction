<?php

namespace App\Entity\Theme;

use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Theme\WebsiteThemeAssetRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function Symfony\Component\String\u;

use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WebsiteThemeAssetRepository::class)]
#[ORM\Table('website_themes_assets')]
class WebsiteThemeAsset
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: WebsiteTheme::class, inversedBy: 'assets')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private WebsiteTheme $theme;

    #[ORM\Column(length: 250)]
    private string $pathname;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private Upload $file;

    public function __construct(WebsiteTheme $theme, string $pathname, Upload $file)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->theme = $theme;
        $this->pathname = $pathname;
        $this->file = $file;
    }

    public static function createFromUpload(WebsiteTheme $theme, UploadedFile $file, Upload $upload): self
    {
        $name = u($file->getClientOriginalName())->replace('.'.$file->getClientOriginalExtension(), '');
        $name = (new AsciiSlugger())->slug($name)->lower()->slice(0, 95).'.'.$file->guessExtension();

        return new self($theme, $name, $upload);
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['theme'], $data['pathname'] ?? $data['file']->getPathname(), $data['file']);
        $self->createdAt = $data['createdAt'] ?? new \DateTime();

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function getTheme(): ?WebsiteTheme
    {
        return $this->theme;
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getFile(): Upload
    {
        return $this->file;
    }
}
