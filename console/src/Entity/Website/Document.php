<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\DocumentData;
use App\Repository\Website\DocumentRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function Symfony\Component\String\u;

use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table('website_documents')]
class Document
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;
    use Util\EntityMemberRestrictedTrait;

    /**
     * Configurable document name for readability.
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

    public static function createFromData(Project $project, DocumentData $data, Upload $upload): self
    {
        $name = u($data->file->getClientOriginalName())->replace('.'.$data->file->getClientOriginalExtension(), '');
        $name = (new AsciiSlugger())->slug($name)->lower()->slice(0, 95).'.'.$upload->getExtension();

        return new self($project, $name, $upload);
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['name'] ?? $data['file']->getPathname(), $data['file']);
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;

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
