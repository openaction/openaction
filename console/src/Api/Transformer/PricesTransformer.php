<?php

namespace App\Api\Transformer;

use App\Platform\Prices;

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
        ];
    }
}
