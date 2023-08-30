<?php

namespace App\Validator;

use App\Entity\Community\ContactUpdate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ContactUpdateTokenValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint ContactUpdateToken */
        if (!$constraint instanceof ContactUpdateToken) {
            throw new UnexpectedTypeException($constraint, ContactUpdateToken::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var ContactUpdate $value */
        if (!$value instanceof ContactUpdate) {
            $this->context->buildViolation($constraint->message)
                ->setCode(ContactUpdateToken::INVALID_INSTANCE)
                ->addViolation();

            return;
        }

        if ($value->getToken() !== $constraint->token) {
            $this->context->buildViolation($constraint->message)
                ->setCode(ContactUpdateToken::INVALID_TOKEN)
                ->addViolation();

            return;
        }

        $expire = new \DateTime();
        $expire->sub(new \DateInterval('P'.$constraint->daysToExpire.'D'));

        if ($expire->getTimestamp() > $value->getRequestedAt()->getTimestamp()) {
            $this->context->buildViolation($constraint->message)
                ->setCode(ContactUpdateToken::TOKEN_EXPIRE)
                ->addViolation();

            return;
        }
    }
}
