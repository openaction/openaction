<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\PhoningCampaignCallRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PhoningCampaignCallRepository::class)]
#[ORM\Table('community_phoning_campaigns_calls')]
class PhoningCampaignCall
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    public const STATUS_CALLING = 'calling';
    public const STATUS_FAILED_INVALID = 'failed_invalid';
    public const STATUS_FAILED_NO_ANSWER = 'failed_no_answer';
    public const STATUS_FAILED_NO_CALL = 'failed_no_call';
    public const STATUS_FAILED_UNREGISTER = 'failed_unregister';
    public const STATUS_FAILED_CALL_LATER = 'failed_call_later';
    public const STATUS_ACCEPTED = 'accepted';

    #[ORM\ManyToOne(targetEntity: PhoningCampaignTarget::class, inversedBy: 'calls', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PhoningCampaignTarget $target;

    #[ORM\ManyToOne(targetEntity: Contact::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $author;

    #[ORM\Column(length: 25)]
    private string $status;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $date;

    public function __construct(PhoningCampaignTarget $target, Contact $author, \DateTime $date = null)
    {
        $this->uuid = Uid::random();
        $this->target = $target;
        $this->author = $author;
        $this->status = self::STATUS_CALLING;
        $this->date = $date ?? new \DateTime();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['target'], $data['author'], $data['date'] ?? new \DateTime());

        if ($data['status'] ?? null) {
            $self->setStatus($data['status']);
        }

        if ($data['uuid'] ?? false) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_CALLING,
            self::STATUS_FAILED_INVALID,
            self::STATUS_FAILED_NO_ANSWER,
            self::STATUS_FAILED_NO_CALL,
            self::STATUS_FAILED_UNREGISTER,
            self::STATUS_FAILED_CALL_LATER,
            self::STATUS_ACCEPTED,
        ];
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getTarget(): PhoningCampaignTarget
    {
        return $this->target;
    }

    public function getAuthor(): Contact
    {
        return $this->author;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }
}
