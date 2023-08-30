<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Form;
use App\Util\Uid;

class FormPartialTransformer extends AbstractTransformer
{
    public function transform(Form $form)
    {
        return [
            '_resource' => 'Form',
            '_links' => [
                'self' => (
                    $form->isOnlyForMembers()
                        ? $this->createLink('api_area_forms_view', ['id' => Uid::toBase62($form->getUuid())])
                        : $this->createLink('api_website_forms_view', ['id' => Uid::toBase62($form->getUuid())])
                ),
                'answer' => $this->createLink('api_website_forms_answer', ['id' => Uid::toBase62($form->getUuid())]),
            ],
            'id' => Uid::toBase62($form->getUuid()),
            'title' => $form->getTitle(),
            'slug' => $form->getSlug(),
            'description' => $form->getDescription(),
            'proposeNewsletter' => $form->proposeNewsletter(),
            'redirectUrl' => $form->getRedirectUrl(),
            'phoningCampaignId' => $form->getPhoningCampaign() ? Uid::toBase62($form->getPhoningCampaign()->getUuid()) : null,
        ];
    }

    public static function describeResourceName(): string
    {
        return 'FormPartial';
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
        ];
    }
}
