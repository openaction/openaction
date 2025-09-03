<?php

namespace App\Entity\Community;

use App\Entity\Community\Model\MembershipPlanType;
use App\Entity\Project;
use App\Entity\Util;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('community_contacts_memberships')]
class MembershipPlan
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\Column(length: 50, nullable: true, enumType: MembershipPlanType::class)]
    private ?MembershipPlanType $type;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $mollieExternalId;
}
