<?php

namespace App\Validator;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidAreaValidator extends ConstraintValidator
{
    private CitipoInterface $citipo;
    private RequestStack $requestStack;

    public function __construct(CitipoInterface $citipo, RequestStack $requestStack)
    {
        $this->citipo = $citipo;
        $this->requestStack = $requestStack;
    }

    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof ValidArea) {
            throw new UnexpectedTypeException($constraint, ValidArea::class);
        }

        $country = $entity->{$constraint->countryField} ?? null;
        $zipCode = str_replace([' ', "\t"], '', trim($entity->{$constraint->zipCodeField} ?? null));

        if (!$country || !$zipCode) {
            return;
        }

        if (!in_array($country, $constraint->checkCountries, true)) {
            return;
        }

        if (!$apiToken = $this->requestStack->getCurrentRequest()->attributes->get('api_token')) {
            return;
        }

        $validation = $this->citipo->validateArea($apiToken, $country, $zipCode);

        if ('ok' !== $validation->status) {
            $this->context->addViolation($constraint->message);
        }
    }
}
