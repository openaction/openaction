<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\FormAnswer;
use App\Util\Uid;

class FormAnswerTransformer extends AbstractTransformer
{
    public function transform(FormAnswer $answer)
    {
        return [
            '_resource' => 'FormAnswer',
            '_links' => [
                'form' => (
                    $answer->getForm()->isOnlyForMembers()
                        ? $this->createLink('api_area_forms_view', ['id' => Uid::toBase62($answer->getForm()->getUuid())])
                        : $this->createLink('api_website_forms_view', ['id' => Uid::toBase62($answer->getForm()->getUuid())])
                ),
            ],
            'id' => Uid::toBase62($answer->getUuid()),
            'contactId' => $answer->getContact() ? Uid::toBase62($answer->getContact()->getUuid()) : null,
            'answers' => $answer->getAnswers(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'FormAnswer';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'form' => 'string',
            ],
            'id' => 'string',
            'contactId' => 'string',
            'answers' => 'array',
        ];
    }
}
