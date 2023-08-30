<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Search\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a request to apply an action to a batch of results coming from the CRM
 * (export, add tags, visualize on a map, ...).
 */
class BatchRequest
{
    #[Assert\Type(['string', 'null'])]
    public $queryInput;

    #[Assert\All([new Assert\Type(['string'])])]
    #[Assert\Type(['array', 'null'])]
    public $queryFilter;

    #[Assert\All([new Assert\Type(['string'])])]
    #[Assert\Type(['array', 'null'])]
    public $querySort;

    #[Assert\Type(['array', 'null'])]
    public $params;

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        foreach (get_object_vars($self) as $var => $value) {
            $self->{$var} = $data[$var] ?? null;
        }

        return $self;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
