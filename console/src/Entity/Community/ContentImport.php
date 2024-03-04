<?php

namespace App\Entity\Community;

use App\Entity\Community\Model\ContentImportSettings;
use App\Entity\Platform\Job;
use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Community\ContentImportRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ContentImportRepository::class)]
#[ORM\Table('projects_content_imports')]
class ContentImport
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    public const WORDPRESS_CONTENT_TYPE_PAGE = 'page';
    public const WORDPRESS_CONTENT_TYPE_POST = 'post';
    public const WORDPRESS_CONTENT_TYPE_ATTACHMENT = 'attachment';

    #[ORM\Column(type: 'string', length: 20)]
    private string $source;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $settings;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Upload $file;

    #[ORM\OneToOne(targetEntity: Job::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Job $job;

    public function __construct(Project $project, Upload $file, string $source, array $settings = [])
    {
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->file = $file;
        $this->source = $source;
        $this->settings = $settings;
        $this->job = new Job('import', 0, 0);
    }

    public function getFile(): Upload
    {
        return $this->file;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSelectedSettings(ContentImportSettings $settings): void
    {
        $this->settings = get_object_vars($settings);
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['file'], $data['source'], $data['settings']);

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }
}
