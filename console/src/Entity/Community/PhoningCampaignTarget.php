<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Entity\Website\FormAnswer;
use App\Repository\Community\PhoningCampaignTargetRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PhoningCampaignTargetRepository::class)]
#[ORM\Table('community_phoning_campaigns_targets')]
class PhoningCampaignTarget
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: PhoningCampaign::class, inversedBy: 'targets', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PhoningCampaign $campaign;

    #[ORM\ManyToOne(targetEntity: Contact::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\OneToOne(targetEntity: FormAnswer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?FormAnswer $answer;

    /**
     * @var Collection|PhoningCampaignCall[]
     */
    #[ORM\OneToMany(targetEntity: PhoningCampaignCall::class, mappedBy: 'target', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Collection $calls;

    public function __construct(PhoningCampaign $campaign, Contact $contact)
    {
        $this->uuid = Uid::random();
        $this->campaign = $campaign;
        $this->contact = $contact;
        $this->calls = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['campaign'], $data['contact']);

        if ($data['answer'] ?? false) {
            $self->setAnswer($data['answer']);
        }

        if ($data['uuid'] ?? false) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function setAnswer(FormAnswer $answer): void
    {
        $this->answer = $answer;
    }

    public function getCampaign(): PhoningCampaign
    {
        return $this->campaign;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getAnswer(): ?FormAnswer
    {
        return $this->answer;
    }

    /**
     * @return Collection|PhoningCampaignCall[]
     */
    public function getCalls(): Collection
    {
        return $this->calls;
    }
}
