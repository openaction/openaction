<?php

namespace App\Entity\Community;

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

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Upload $file;

    #[ORM\OneToOne(targetEntity: Job::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Job $job;

    public function __construct(Organization $organization, Upload $file)
    {
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->file = $file;
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

    // TODO! Add fixtures
}
