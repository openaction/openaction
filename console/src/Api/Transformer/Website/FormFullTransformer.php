<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Form;
use App\Entity\Website\FormBlock;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class FormFullTransformer extends AbstractTransformer
{
    private FormPartialTransformer $partialTransformer;
    private FormBlockTransformer $formBlockTransformer;

    public function __construct(FormPartialTransformer $partialTransformer, FormBlockTransformer $formBlockTransformer)
    {
        $this->partialTransformer = $partialTransformer;
        $this->formBlockTransformer = $formBlockTransformer;
    }

    public function transform(Form $form)
    {
        // Automatically add the newsletter block if requested
        $blocks = $form->getBlocks()->toArray();
        if ($form->proposeNewsletter()) {
            if (!$form->hasEmailBlock()) {
                $blocks[] = new FormBlock($form, FormBlock::TYPE_EMAIL, 'Email', true);
            }

            $blocks[] = new FormBlock($form, FormBlock::TYPE_NEWSLETTER, 'Newsletter');
        }

        // Transform
        $item = $this->partialTransformer->transform($form);
        $item['blocks']['data'] = [];

        foreach ($blocks as $block) {
            $item['blocks']['data'][] = $this->formBlockTransformer->transform($block);
        }

        return $item;
    }

    public static function describeResourceName(): string
    {
        return 'FormFull';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
                'answer' => 'string',
            ],
            'id' => 'string',
            'title' => 'string',
            'slug' => 'string',
            'description' => 'string',
            'proposeNewsletter' => 'boolean',
            'phoningCampaignId' => '?string',
            'blocks' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/FormBlock']),
                ]),
            ],
        ];
    }
}
