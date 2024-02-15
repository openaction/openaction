<?php

namespace App\Entity\Community;

use App\Entity\Community\Model\ContentImportSettings;
use App\Entity\Organization;
use App\Entity\Platform\Job;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Community\ContentImportRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentImportRepository::class)]
#[ORM\Table('project_content_imports')]
class ContentImport
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

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

    public function __construct(Organization $organization, Upload $file, string $source)
    {
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->file = $file;
        $this->source = $source;
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

    // TODO! Add fixtures
}
