<?php

namespace App\Api\Transformer;

use App\Platform\Prices;
use App\Platform\PrintFiles;
use App\Platform\Products;

class PricesTransformer extends AbstractTransformer
{
    public function transform()
    {
        return [
            '_resource' => 'Prices',
            'credits' => [
                'email' => Prices::CREDIT_EMAIL,
                'text' => Prices::CREDIT_TEXT,
            ],
            'print_products' => Prices::PRINT_PRODUCTION,
            'print_weights' => PrintFiles::WEIGHT_BY_PRODUCT,
            'print_delivery' => Prices::PRINT_DELIVERY,
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Prices';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'credits' => [
                'email' => 'float',
                'text' => 'float',
            ],
            'print_products' => [
                Products::PRINT_OFFICIAL_POSTER => 'float[]',
                Products::PRINT_OFFICIAL_BANNER => 'float[]',
                Products::PRINT_OFFICIAL_PLEDGE => 'float[]',
                Products::PRINT_OFFICIAL_BALLOT => 'float[]',
                Products::PRINT_CAMPAIGN_FLYER => 'float[]',
                Products::PRINT_CAMPAIGN_LARGE_FLYER => 'float[]',
                Products::PRINT_CAMPAIGN_DOOR => 'float[]',
                Products::PRINT_CAMPAIGN_BOOKLET_4 => 'float[]',
                Products::PRINT_CAMPAIGN_BOOKLET_8 => 'float[]',
                Products::PRINT_CAMPAIGN_LETTER => 'float[]',
                Products::PRINT_CAMPAIGN_POSTER => 'float[]',
                Products::PRINT_CAMPAIGN_CARD => 'float[]',
            ],
            'print_weights' => [
                Products::PRINT_OFFICIAL_POSTER => 'float[]',
                Products::PRINT_OFFICIAL_BANNER => 'float[]',
                Products::PRINT_OFFICIAL_PLEDGE => 'float[]',
                Products::PRINT_OFFICIAL_BALLOT => 'float[]',
                Products::PRINT_CAMPAIGN_FLYER => 'float[]',
                Products::PRINT_CAMPAIGN_LARGE_FLYER => 'float[]',
                Products::PRINT_CAMPAIGN_DOOR => 'float[]',
                Products::PRINT_CAMPAIGN_BOOKLET_4 => 'float[]',
                Products::PRINT_CAMPAIGN_BOOKLET_8 => 'float[]',
                Products::PRINT_CAMPAIGN_LETTER => 'float[]',
                Products::PRINT_CAMPAIGN_POSTER => 'float[]',
                Products::PRINT_CAMPAIGN_CARD => 'float[]',
            ],
            'print_delivery' => 'float[][]',
        ];
    }
}
