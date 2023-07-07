<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use App\Entity\Community\Contact;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ContactUpdateEmailApiData
{
    #[Assert\Type(['string', 'null'])]
    #[Assert\Email]
    #[Assert\Length(max: 250)]
    public $newEmail;

    private Contact $contact;

    public static function createFromPayload(array $data, Contact $contact): self
    {
        $self = new self();
        $self->newEmail = $data['newEmail'] ?? null;
        $self->contact = $contact;

        return $self;
    }

    #[Assert\Callback]
    public function validateEqualEmail(ExecutionContextInterface $context)
    {
        if ($this->newEmail === $this->contact->getEmail()) {
            $context->buildViolation('console.organization.community.email_not_equal')
                ->atPath('newEmail')
                ->addViolation()
            ;
        }
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }
}
