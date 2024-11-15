<?php

namespace App\Community;

use App\Entity\Area;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\PhoningCampaign;
use App\Entity\Community\TextingCampaign;
use App\Entity\Organization;
use App\Entity\Project;
use App\Repository\AreaRepository;
use App\Repository\Community\ContactRepository;
use App\Util\Uid;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ContactViewBuilder
{
    public const FILTER_OR = 'or';
    public const FILTER_AND = 'and';

    private ContactRepository $repository;
    private AreaRepository $areaRepository;

    private ?Organization $organization = null;
    private ?Project $project = null;
    private array $areasFilter = [];
    private string $tagsFilterType = self::FILTER_OR;
    private array $tagsFilter = [];
    private array $emailsFilter = [];
    private array $phonesFilter = [];
    private bool $havingParsedPhone = false;
    private bool $onlyMembers = false;
    private bool $onlyNewsletter = false;
    private bool $onlySms = false;
    private bool $onlyCalls = false;
    private ?OrderBy $orderBy = null;
    private ?int $offset = null;
    private ?int $limit = null;

    public function __construct(ContactRepository $repository, AreaRepository $areaRepository)
    {
        $this->repository = $repository;
        $this->areaRepository = $areaRepository;
    }

    public function forEmailingCampaign(EmailingCampaign $campaign): self
    {
        return $this
            ->onlyNewsletterSubscribers()
            ->inProject($campaign->getProject())
            ->onlyMembers($campaign->isOnlyForMembers())
            ->inAreas($campaign->getAreasFilterIds())
            ->withTags($campaign->getTagsFilterIds(), $campaign->getTagsFilterType())
            ->withEmails($campaign->getContactsFilter())
        ;
    }

    public function forTextingCampaign(TextingCampaign $campaign): self
    {
        return $this
            ->onlySmsSubscribers()
            ->havingParsedPhone()
            ->inProject($campaign->getProject())
            ->onlyMembers($campaign->isOnlyForMembers())
            ->inAreas($campaign->getAreasFilterIds())
            ->withTags($campaign->getTagsFilterIds(), $campaign->getTagsFilterType())
            ->withPhones($campaign->getContactsFilter())
        ;
    }

    public function forPhoningCampaign(PhoningCampaign $campaign): self
    {
        return $this
            ->onlyCallsSubscribers()
            ->havingParsedPhone()
            ->inProject($campaign->getProject())
            ->onlyMembers($campaign->isOnlyForMembers())
            ->inAreas($campaign->getAreasFilterIds())
            ->withTags($campaign->getTagsFilterIds(), $campaign->getTagsFilterType())
            ->withPhones($campaign->getContactsFilter())
        ;
    }

    public function inOrganization(Organization $organization): self
    {
        $self = clone $this;
        $self->organization = $organization;

        return $self;
    }

    public function inProject(Project $project): self
    {
        $self = clone $this;
        $self->organization = $project->getOrganization();
        $self->project = $project;

        return $self;
    }

    public function inAreas(?array $areasFilter): self
    {
        $self = clone $this;
        $self->areasFilter = $areasFilter ?: [];

        return $self;
    }

    public function havingParsedPhone(): self
    {
        $self = clone $this;
        $self->havingParsedPhone = true;

        return $self;
    }

    public function withTags(?array $tagsFilter, string $tagsFilterType): self
    {
        $self = clone $this;
        $self->tagsFilter = $tagsFilter ?: [];
        $self->tagsFilterType = $tagsFilterType;

        return $self;
    }

    public function withEmails(?array $emailsFilter): self
    {
        $self = clone $this;
        $self->emailsFilter = $emailsFilter ?: [];

        return $self;
    }

    public function withPhones(?array $phonesFilter): self
    {
        $self = clone $this;
        $self->phonesFilter = $phonesFilter ?: [];

        return $self;
    }

    public function onlyMembers(bool $onlyMembers = true): self
    {
        $self = clone $this;
        $self->onlyMembers = $onlyMembers;

        return $self;
    }

    public function onlyNewsletterSubscribers(bool $onlyNewsletter = true): self
    {
        $self = clone $this;
        $self->onlyNewsletter = $onlyNewsletter;

        return $self;
    }

    public function onlySmsSubscribers(bool $onlySms = true): self
    {
        $self = clone $this;
        $self->onlySms = $onlySms;

        return $self;
    }

    public function onlyCallsSubscribers(bool $onlyCalls = true): self
    {
        $self = clone $this;
        $self->onlyCalls = $onlyCalls;

        return $self;
    }

    public function orderBy(string $field, string $order = 'ASC'): self
    {
        $self = clone $this;
        $self->orderBy = new OrderBy('c.'.$field, $order);

        return $self;
    }

    public function setPage(int $page, int $limit): self
    {
        $self = clone $this;
        $self->offset = ($page - 1) * $limit;
        $self->limit = $limit;

        return $self;
    }

    public function count(): int
    {
        return $this->paginate()->count();
    }

    public function paginate(): Paginator
    {
        $query = $this->createQueryBuilder()
            ->select('c', 't')
            ->leftJoin('c.metadataTags', 't')
            ->getQuery()
        ;

        // Result range
        if ($this->limit) {
            $query->setMaxResults($this->limit);
            $query->setFirstResult($this->offset);
        }

        return new Paginator($query, true);
    }

    public function createQueryBuilder()
    {
        if (!$this->organization) {
            throw new \LogicException('ContactViewBuilder::createQueryBuilder() requires an organization to be provided.');
        }

        $qb = $this->repository->createQueryBuilder('c');
        $filterQb = $this->repository->createQueryBuilder('sc');

        // In organization
        $filterQb->andWhere('sc.organization = :orga');
        $qb->setParameter('orga', $this->organization->getId());

        // In local project or area
        if ($this->areasFilter || ($this->project && $this->project->isLocal())) {
            $filterQb->leftJoin('sc.area', 'a');
            $filterQb->andWhere('sc.area IS NOT NULL');

            if ($this->project && $this->project->isLocal()) {
                $projectAreaOr = $filterQb->expr()->orX();
                foreach ($this->project->getAreas() as $k => $area) {
                    $projectAreaOr->add('a.treeLeft >= :projectTreeLeft'.$k.' AND a.treeRight <= :projectTreeRight'.$k);
                    $qb->setParameter('projectTreeLeft'.$k, $area->getTreeLeft());
                    $qb->setParameter('projectTreeRight'.$k, $area->getTreeRight());
                }

                $filterQb->andWhere($projectAreaOr);
            }

            if ($this->areasFilter) {
                $filterAreaOr = $filterQb->expr()->orX();
                foreach ($this->areaRepository->findFilter($this->areasFilter) as $k => $area) {
                    $filterAreaOr->add('a.treeLeft >= :areaTreeLeft'.$k.' AND a.treeRight <= :areaTreeRight'.$k);
                    $qb->setParameter('areaTreeLeft'.$k, $area->getTreeLeft());
                    $qb->setParameter('areaTreeRight'.$k, $area->getTreeRight());
                }

                $filterQb->andWhere($filterAreaOr);
            }
        }

        // In thematic project or tags
        if ($this->tagsFilter || ($this->project && $this->project->isThematic())) {
            if ($this->project && $this->project->isThematic()) {
                $filterQb->leftJoin('sc.metadataTags', 'stp');

                $tagCond = $filterQb->expr()->orX();
                foreach ($this->project->getTags() as $k => $tag) {
                    $tagCond->add('stp.id = :projectTag'.$k);
                    $qb->setParameter('projectTag'.$k, $tag->getId());
                }

                $filterQb->andWhere($tagCond);
            }

            if ($this->tagsFilter) {
                $filterQb->leftJoin('sc.metadataTags', 'stf');

                $tagCond = $filterQb->expr()->orX();
                foreach ($this->tagsFilter as $k => $tagId) {
                    $tagCond->add('stf.id = :filterTag'.$k);
                    $qb->setParameter('filterTag'.$k, $tagId);
                }

                $filterQb->andWhere($tagCond);

                if (self::FILTER_AND === $this->tagsFilterType) {
                    $filterQb->having('COUNT(DISTINCT stf.id) = '.count($this->tagsFilter));
                    $filterQb->groupBy('sc.id');
                }
            }
        }

        // Only members
        if ($this->onlyMembers) {
            $filterQb->andWhere($filterQb->expr()->isNotNull('sc.accountPassword'));
        }

        // Only newsletter subscribers
        if ($this->onlyNewsletter) {
            // Require email
            $filterQb->andWhere('sc.email IS NOT NULL');

            if ($this->project) {
                $onlyNewsLetterCond = $filterQb->expr()->andX();
                $onlyNewsLetterCond->add('sc.settingsReceiveNewsletters = TRUE');
                $onlyNewsLetterCond->add(sprintf("CONTAINS(sc.settingsByProject, '[{\"projectId\": \"%s\", \"settingsReceiveNewsletters\": false}]') = FALSE", Uid::toBase62($this->project->getUuid())));
                $filterQb->andWhere($onlyNewsLetterCond);
            } else {
                $filterQb->andWhere('sc.settingsReceiveNewsletters = TRUE');
            }
        }

        // Only SMS subscribers
        if ($this->onlySms) {
            if ($this->project) {
                $onlySmsCond = $filterQb->expr()->orX();
                $onlySmsCond->add('sc.settingsReceiveSms = TRUE');
                $onlySmsCond->add(sprintf("CONTAINS(sc.settingsByProject, '[{\"projectId\": \"%s\", \"settingsReceiveSms\": true}]') = TRUE", Uid::toBase62($this->project->getUuid())));
                $filterQb->andWhere($onlySmsCond);
            } else {
                $filterQb->andWhere('sc.settingsReceiveSms = TRUE');
            }
        }

        // Only calls subscribers
        if ($this->onlyCalls) {
            if ($this->project) {
                $onlyCallsCond = $filterQb->expr()->orX();
                $onlyCallsCond->add('sc.settingsReceiveCalls = TRUE');
                $onlyCallsCond->add(sprintf("CONTAINS(sc.settingsByProject, '[{\"projectId\": \"%s\", \"settingsReceiveCalls\": true}]') = TRUE", Uid::toBase62($this->project->getUuid())));
                $filterQb->andWhere($onlyCallsCond);
            } else {
                $filterQb->andWhere('sc.settingsReceiveCalls = TRUE');
            }
        }

        // Emails
        if ($emails = array_filter(array_map('trim', array_filter($this->emailsFilter)))) {
            $filterQb->andWhere($filterQb->expr()->in('sc.email', $emails));
        }

        // Having a phone
        if ($this->havingParsedPhone) {
            $filterQb->andWhere('sc.parsedContactPhone IS NOT NULL');
        }

        // Phones
        if ($this->phonesFilter) {
            $filterQb->andWhere($filterQb->expr()->in('sc.parsedContactPhone', $this->phonesFilter));
        }

        // Order
        $qb->orderBy($this->orderBy ?: new OrderBy('c.id', 'ASC'));

        // Apply filter
        $qb->andWhere($qb->expr()->in('c.id', $filterQb->getDQL()));

        return $qb;
    }
}
