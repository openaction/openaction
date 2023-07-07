<?php

namespace App\Community\Ambiguity;

use App\Entity\Community\Ambiguity;
use App\Entity\Community\Contact;
use App\Repository\Community\AmbiguityRepository;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ContactMerger
{
    public function __construct(
        private EntityManagerInterface $em,
        private AmbiguityRepository $repository,
        private MessageBusInterface $bus,
    ) {
    }

    public function merge(Ambiguity $ambiguity, string $type)
    {
        /** @var Contact $newest */
        $newest = $ambiguity->getNewest();

        /** @var Contact $oldest */
        $oldest = $ambiguity->getOldest();

        // Email
        $this->mergeEmail($oldest, $newest, $type);

        // Area
        $this->mergeArea($oldest, $newest);

        // Tags
        foreach ($newest->getMetadataTags() as $tag) {
            if (!$oldest->hasMetadataTag($tag)) {
                $tag->getContacts()->add($oldest);
                $oldest->getMetadataTags()->add($tag);
            }
        }

        // Settings
        $oldest->applySettingsUpdate(
            $newest->hasSettingsReceiveNewsletters(),
            $newest->hasSettingsReceiveSms(),
            $newest->hasSettingsReceiveCalls(),
            source: 'merge',
        );

        // Custom fields
        $oldest->mergeMetadataCustomFields($newest->getMetadataCustomFields());

        // Comment
        $oldest->mergeMetadataComment($newest->getMetadataComment());

        // Other fields
        $this->mergeOtherFields($oldest, $newest);

        // Persist
        $this->em->wrapInTransaction(function () use ($ambiguity) {
            $this->repository->removeRelationshipsToContactNewest($ambiguity);
            $this->repository->applyUpdateToContactNewest($ambiguity);
        });

        // Update CRM search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($oldest));
        $this->bus->dispatch(new RemoveCrmDocumentMessage($newest->getOrganization()->getId(), $newest->getUuid()->toRfc4122()));
    }

    private function mergeEmail(Contact $oldest, Contact $newest, string $type)
    {
        $nextEmail = $newest->getEmail();
        $additionalEmail = $oldest->getEmail();

        // If the oldest email is requested,
        // or if the old one is a member,
        // or if the newest email is empty
        if ('oldest' === $type || !$newest->getEmail() || ($oldest->isMember() && !$newest->isMember())) {
            $nextEmail = $oldest->getEmail();
            $additionalEmail = $newest->getEmail();
        }

        $oldest->changeEmail($nextEmail);
        $oldest->setContactAdditionalEmails([
            ...$oldest->getContactAdditionalEmails(),
            ...$newest->getContactAdditionalEmails(),
            $additionalEmail,
        ]);
    }

    private function mergeArea(Contact $oldest, Contact $newest)
    {
        $areaOldest = $oldest->getArea();
        $areaNewest = $newest->getArea();

        if (!$areaOldest || ($areaNewest && $areaNewest->getTreeLevel() > $areaOldest->getTreeLevel())) {
            $oldest->setArea($areaNewest);
        }
    }

    private function mergeOtherFields(Contact $oldest, Contact $newest)
    {
        $metadata = $this->em->getClassMetadata(Contact::class);
        $fieldNames = array_diff($metadata->getFieldNames(), [
            'id',
            'uuid',
            'createdAt',
            'updatedAt',
            'email',
            'contactAdditionalEmails',
            'settingsReceiveNewsletters',
            'settingsReceiveSms',
            'settingsReceiveCalls',
            'metadataComment',
            'metadataCustomFields',
        ]);

        foreach ($fieldNames as $fieldName) {
            if (null !== ($value = $metadata->getFieldValue($newest, $fieldName))) {
                $metadata->setFieldValue($oldest, $fieldName, $value);
            }
        }
    }
}
