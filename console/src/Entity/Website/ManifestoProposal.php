<?php

namespace App\Entity\Website;

use App\Entity\Util;
use App\Form\Website\Model\ManifestoProposalData;
use App\Repository\Website\ManifestoProposalRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ManifestoProposalRepository::class)]
#[ORM\Table('website_manifestos_proposals')]
class ManifestoProposal implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    public const STATUS_NONE = null;
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';

    #[ORM\ManyToOne(targetEntity: ManifestoTopic::class, inversedBy: 'proposals')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ManifestoTopic $topic;

    #[ORM\Column(length: 250)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $statusDescription = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $statusCtaText = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $statusCtaUrl = null;

    #[ORM\Column(type: 'smallint')]
    private int $weight;

    public function __construct(ManifestoTopic $topic, string $title, int $weight)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->topic = $topic;
        $this->title = $title;
        $this->weight = $weight;
    }

    /*
     * Factories
     */

    public static function createFixture(array $data): self
    {
        $self = new self($data['topic'], $data['title'], $data['weight'] ?? 1);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['title']);
        $self->content = $data['content'] ?? '';
        $self->status = $data['status'] ?? null;
        $self->statusDescription = $data['statusDescription'] ?? null;
        $self->statusCtaText = $data['statusCtaText'] ?? null;
        $self->statusCtaUrl = $data['statusCtaUrl'] ?? null;

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->topic, $this->title, $this->weight + 1);
        $self->content = $this->content;

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'manifesto-proposal';
    }

    public function isSearchPublic(): bool
    {
        return $this->getTopic()->isPublished();
    }

    public function getSearchOrganization(): string
    {
        return $this->getTopic()->getSearchOrganization();
    }

    public function getSearchAccessibleFromProjects(): array
    {
        return $this->getTopic()->getSearchAccessibleFromProjects();
    }

    public function getSearchUuid(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function getSearchTitle(): string
    {
        return $this->title;
    }

    public function getSearchContent(): ?string
    {
        return strip_tags($this->content);
    }

    public function getSearchCategoriesFacet(): array
    {
        return [$this->getTopic()->getTitle()];
    }

    public function getSearchStatusFacet(): string
    {
        return $this->getTopic()->getSearchStatusFacet();
    }

    public function getSearchAreaTreeFacet(): array
    {
        return [];
    }

    public function getSearchDateFacet(): ?int
    {
        return $this->getTopic()->getSearchDateFacet();
    }

    public function getSearchMetadata(): array
    {
        return [
            'project' => $this->topic->getProject()->getUuid()->toRfc4122(),
            'projectName' => $this->topic->getProject()->getName(),
            'topic' => $this->topic->getUuid()->toRfc4122(),
            'topicName' => $this->topic->getTitle(),
        ];
    }

    /*
     * Setters
     */
    public function setTopic(ManifestoTopic $topic)
    {
        $this->topic = $topic;
    }

    public function applyUpdate(ManifestoProposalData $data)
    {
        $this->title = (string) $data->title;
        $this->content = (string) $data->content;
        $this->status = $data->status;
        $this->statusDescription = $data->statusDescription;
        $this->statusCtaText = $data->statusCtaText;
        $this->statusCtaUrl = $data->statusCtaUrl;
    }

    /*
     * Getters
     */
    public function getTopic(): ManifestoTopic
    {
        return $this->topic;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getStatusDescription(): ?string
    {
        return $this->statusDescription;
    }

    public function getStatusCtaText(): ?string
    {
        return $this->statusCtaText;
    }

    public function getStatusCtaUrl(): ?string
    {
        return $this->statusCtaUrl;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
