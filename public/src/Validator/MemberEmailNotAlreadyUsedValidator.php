<?php

namespace App\Validator;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MemberEmailNotAlreadyUsedValidator extends ConstraintValidator
{
    private CitipoInterface $citipo;
    private RequestStack $requestStack;

    public function __construct(CitipoInterface $citipo, RequestStack $requestStack)
    {
        $this->citipo = $citipo;
        $this->requestStack = $requestStack;
    }

    public function validate($email, Constraint $constraint)
    {
        if (!$constraint instanceof MemberEmailNotAlreadyUsed) {
            throw new UnexpectedTypeException($constraint, MemberEmailNotAlreadyUsed::class);
        }

        if (null === $email || '' === $email) {
            return;
        }

        if (!is_string($email)) {
            throw new UnexpectedTypeException($email, 'string');
        }

        if (!$apiToken = $this->requestStack->getCurrentRequest()->attributes->get('api_token')) {
            return;
        }

        $statusCheck = $this->citipo->getContactStatus($apiToken, $email);

        if ('member' === $statusCheck->status) {
            $this->context->addViolation($constraint->message);
        }
    }
}
