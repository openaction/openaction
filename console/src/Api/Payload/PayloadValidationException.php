<?php

declare(strict_types=1);

namespace App\Api\Payload;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class PayloadValidationException extends \Exception
{
    public function __construct(private readonly ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct();
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }
}
