<?php

namespace App\Api\Transformer\Validator;

use App\Api\Transformer\AbstractTransformer;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ConstraintViolationTransformer extends AbstractTransformer
{
    public function transform(ConstraintViolationInterface $violation)
    {
        return [
            '_resource' => 'ConstraintViolation',
            'path' => $violation->getPropertyPath(),
            'value' => $violation->getInvalidValue(),
            'message' => $violation->getMessage(),
        ];
    }
}
