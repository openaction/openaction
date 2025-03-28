<?php

namespace App\Entity\Community;

use App\Entity\Area;
use App\Entity\Community\Model\ImportHead;
use App\Entity\Organization;
use App\Entity\Platform\Job;
use App\Entity\Upload;
use App\Entity\Util;
use App\Repository\Community\ImportRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
#[ORM\Table('community_imports')]
class Import
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

    #[ORM\Column(type: 'json')]
    private array $head;

    #[ORM\ManyToOne(targetEntity: Area::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Area $area = null;

    public function __construct(Organization $organization, Upload $file, ImportHead $head)
    {
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->file = $file;
        $this->job = new Job('import', 0, 12);
        $this->head = $head->toArray();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['orga'], $data['file'], $data['head']);
        $self->area = $data['area'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function setArea(?Area $area)
    {
        $this->area = $area;
    }

    public function setMatchedColumns(array $matchedColumns)
    {
        $head = $this->getHead();
        $head->setMatchedColumns($matchedColumns);

        $this->head = $head->toArray();
    }

    public function getFile(): Upload
    {
        return $this->file;
    }

    public function getHead(): ImportHead
    {
        return ImportHead::createFromData($this->head);
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function getJob(): Job
    {
        return $this->job;
    }
}
