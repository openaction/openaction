<?php

namespace App\Api\Transformer\Integrations;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\OrganizationMember;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class OrganizationMemberTransformer extends AbstractTransformer
{
    public function transform(OrganizationMember $member)
    {
        return [
            '_resource' => 'OrganizationMember',
            'firstName' => $member->getMember()->getFirstName(),
            'lastName' => $member->getMember()->getLastName(),
            'isAdmin' => $member->isAdmin(),
            'externalPermissions' => $member->getLabels(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'OrganizationMember';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'firstName' => 'string',
            'lastName' => 'string',
            'externalPermissions' => new Property([
                'type' => 'array',
                'items' => new Items(['type' => 'string']),
            ]),
        ];
    }
}
