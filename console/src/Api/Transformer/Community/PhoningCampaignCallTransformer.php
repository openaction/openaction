<?php

namespace App\Api\Transformer\Community;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Community\PhoningCampaignCall;
use App\Util\Uid;
use OpenApi\Annotations\Property;

class PhoningCampaignCallTransformer extends AbstractTransformer
{
    private ContactTransformer $contactTransformer;

    public function __construct(ContactTransformer $contactTransformer)
    {
        $this->contactTransformer = $contactTransformer;
    }

    public function transform(PhoningCampaignCall $call)
    {
        return [
            '_resource' => 'PhoningCampaignCall',
            'id' => Uid::toBase62($call->getUuid()),
            'contact' => $this->contactTransformer->transform($call->getTarget()->getContact()),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PhoningCampaignCall';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'id' => 'string',
            'contact' => new Property(['ref' => '#/components/schemas/Contact']),
        ];
    }
}
