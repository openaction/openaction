<?php

namespace App\Bridge\Sendgrid\Model;

use App\Community\MergeTag\ContactMergeTags;
use App\Entity\Community\Contact;
use App\Util\PhoneNumber;
use App\Util\Uid;

use function Symfony\Component\String\u;

class Recipient
{
    private string $email;
    private ?string $messageId;
    private array $vars;

    public function __construct(string $email, ?string $messageId = null, array $vars = [])
    {
        $this->email = $email;
        $this->messageId = $messageId;
        $this->vars = $vars;
    }

    public static function createFromNotification(string $email, ?string $messageId = null, array $vars = []): self
    {
        return new self($email, $messageId, $vars);
    }

    public static function createFromContact(Contact $contact, ?string $messageId = null, array $additionalVariables = []): self
    {
        $vars = ContactMergeTags::withLegacyAliases([
            '-contact-id-' => Uid::toBase62($contact->getUuid()),
            '-contact-email-' => $contact->getEmail() ?: '',
            '-contact-phone-' => PhoneNumber::format($contact->getParsedContactPhone()),
            '-contact-formal-title-' => $contact->getProfileFormalTitle(),
            '-contact-firstname-' => $contact->getProfileFirstName(),
            '-contact-lastname-' => $contact->getProfileLastName(),
            '-contact-fullname-' => $contact->getFullName(),
            '-contact-gender-' => $contact->getProfileGender(),
            '-contact-nationality-' => $contact->getProfileNationality(),
            '-contact-company-' => $contact->getProfileCompany(),
            '-contact-job-title-' => $contact->getProfileJobTitle(),
            '-contact-streetline-1-' => $contact->getAddressStreetLine1(),
            '-contact-streetline-2-' => $contact->getAddressStreetLine2(),
            '-contact-zipcode-' => $contact->getAddressZipCode(),
            '-contact-city-' => $contact->getAddressCity(),
            '-contact-country-' => $contact->getAddressCountry()?->getCode(),
        ]);

        $vars = ContactMergeTags::withLegacyAliases(array_merge($vars, $additionalVariables));

        return new self($contact->getEmail() ?: '', $messageId, $vars);
    }

    public function getEmail(): string
    {
        return u($this->email)->replace(',', '')->replace(';', '')->lower()->toString();
    }

    public function getMessageId(): string
    {
        return $this->messageId ?: '0';
    }

    public function getVariables(): array
    {
        return $this->vars;
    }
}
