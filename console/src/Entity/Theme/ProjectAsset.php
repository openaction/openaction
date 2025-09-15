<?php

namespace App\Entity\Theme;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Theme\ProjectAssetRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: ProjectAssetRepository::class)]
#[ORM\Table('projects_assets')]
class ProjectAsset
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;

    /**
     * Configurable asset name for readability.
     */
    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private Upload $file;

    public function __construct(Project $project, string $name, Upload $file)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->name = $name;
        $this->file = $file;
    }

    public static function createFromUpload(Project $project, UploadedFile $file, Upload $upload): self
    {
        $name = u($file->getClientOriginalName())->replace('.'.$file->getClientOriginalExtension(), '');
        $name = (new AsciiSlugger())->slug($name)->lower()->slice(0, 95).'.'.$file->guessExtension();

        return new self($project, $name, $upload);
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['name'] ?? $data['file']->getPathname(), $data['file']);
        $self->createdAt = $data['createdAt'] ?? new \DateTime();

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFile(): Upload
    {
        return $this->file;
    }
}
