<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use App\Community\ContactViewBuilder;
use Symfony\Component\Validator\Constraints as Assert;

class ContactListApiData
{
    #[Assert\Type(['bool'])]
    public $only_members = false;

    #[Assert\Type(['array'])]
    public $areas_filter = [];

    #[Assert\Type(['array'])]
    public $tags_filter = [];

    #[Assert\Type(['string'])]
    #[Assert\Choice(choices: ['and', 'or'])]
    public $tags_filter_type = ContactViewBuilder::FILTER_OR;

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        foreach (get_object_vars($self) as $var => $value) {
            if (isset($data[$var])) {
                $self->{$var} = $data[$var];
            }
        }

        return $self;
    }
}
