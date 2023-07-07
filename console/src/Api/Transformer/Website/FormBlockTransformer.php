<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\FormBlock;

class FormBlockTransformer extends AbstractTransformer
{
    public function transform(FormBlock $block)
    {
        return [
            '_resource' => 'FormBlock',
            'type' => $block->getType(),
            'content' => $block->getContent(),
            'field' => $block->isField(),
            'required' => $block->isRequired(),
            'config' => $block->getConfig(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'FormBlock';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'type' => 'string',
            'content' => 'string',
            'field' => 'boolean',
            'required' => 'boolean',
            'config' => [],
        ];
    }
}
