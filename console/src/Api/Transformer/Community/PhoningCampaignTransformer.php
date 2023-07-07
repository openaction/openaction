<?php

namespace App\Api\Transformer\Community;

use App\Api\Transformer\AbstractTransformer;
use App\Api\Transformer\Website\FormFullTransformer;
use App\Entity\Community\PhoningCampaign;
use App\Util\Uid;
use OpenApi\Annotations\Property;

class PhoningCampaignTransformer extends AbstractTransformer
{
    private FormFullTransformer $formFullTransformer;

    public function __construct(FormFullTransformer $formFullTransformer)
    {
        $this->formFullTransformer = $formFullTransformer;
    }

    public function transform(PhoningCampaign $campaign)
    {
        return [
            '_resource' => 'PhoningCampaign',
            'id' => Uid::toBase62($campaign->getUuid()),
            'name' => $campaign->getName(),
            'form' => $this->formFullTransformer->transform($campaign->getForm()),
            'startAt' => $campaign->getStartAt()?->format('Y-m-d H:i:s'),
            'endAfter' => $campaign->getEndAfter(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PhoningCampaign';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'id' => 'string',
            'name' => 'string',
            'form' => new Property(['ref' => '#/components/schemas/FormFull']),
            'startAt' => '?string',
            'endAfter' => '?string',
        ];
    }
}
