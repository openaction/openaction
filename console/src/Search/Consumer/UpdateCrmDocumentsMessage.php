<?php

namespace App\Search\Consumer;

use App\Entity\Community\Contact;

final class UpdateCrmDocumentsMessage
{
    public function __construct(
        private string $organizationUuid,
        private string $indexVersion,
        private array $contactsIdentifiers,
    ) {
    }

    public static function forContact(Contact $contact): self
    {
        return new self(
            $contact->getOrganization()->getUuid()->toRfc4122(),
            $contact->getOrganization()->getCrmIndexVersion(),
            [$contact->getUuid()->toRfc4122() => $contact->getId()],
        );
    }

    public function getOrganizationUuid(): string
    {
        return $this->organizationUuid;
    }

    public function getIndexVersion(): string
    {
        return $this->indexVersion;
    }

    /**
     * @return array<string, int>
     */
    public function getContactsIdentifiers(): array
    {
        return $this->contactsIdentifiers;
    }
}
