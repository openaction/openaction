<?php

namespace App\Validator;

use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Repository\Community\ContactRepository;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueContactEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var UniqueContactEmail $constraint */
        if (!$constraint instanceof UniqueContactEmail) {
            throw new UnexpectedTypeException($constraint, UniqueContactEmail::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var Organization $organization */
        $organization = $this->propertyAccessor->getValue($value, $constraint->organizationField);

        /** @var Contact|null $contact */
        $contact = $this->propertyAccessor->getValue($value, $constraint->contactField);

        $email = $this->propertyAccessor->getValue($value, $constraint->emailField);

        if ($this->contactRepository->isEmailAlreadyUsed($organization, $email, $contact?->getId())) {
            $this->context->buildViolation('This value is already used.')
                ->atPath($constraint->emailField)
                ->setCode(UniqueContactEmail::EMAIL_ALREADY_EXISTS)
                ->addViolation();
        }
    }
}
