<?php

namespace App\Api\Persister;

use App\Api\Model\ContactApiData;
use App\Bridge\Integromat\IntegromatInterface;
use App\Bridge\Quorum\QuorumInterface;
use App\Bridge\Spallian\SpallianInterface;
use App\Community\Automation\EmailAutomationDispatcher;
use App\Community\ContactLocator;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactCommitment;
use App\Entity\Community\ContactMandate;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\Enum\ContactMandateType;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Upload;
use App\Mailer\OrganizationMailer;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ContactApiPersister
{
    private EntityManagerInterface $em;
    private ContactRepository $repository;
    private ContactLocator $locator;
    private UserPasswordHasherInterface $hasher;
    private OrganizationMailer $organizationMailer;
    private EmailAutomationDispatcher $automationDispatcher;
    private MessageBusInterface $bus;
    private QuorumInterface $quorum;
    private IntegromatInterface $integromat;
    private SpallianInterface $spallian;

    public function __construct(
        EntityManagerInterface $em,
        ContactRepository $repository,
        ContactLocator $locator,
        UserPasswordHasherInterface $hasher,
        OrganizationMailer $organizationMailer,
        EmailAutomationDispatcher $automationDispatcher,
        MessageBusInterface $bus,
        QuorumInterface $quorum,
        IntegromatInterface $integromat,
        SpallianInterface $spallian,
    ) {
        $this->em = $em;
        $this->repository = $repository;
        $this->locator = $locator;
        $this->hasher = $hasher;
        $this->organizationMailer = $organizationMailer;
        $this->automationDispatcher = $automationDispatcher;
        $this->bus = $bus;
        $this->quorum = $quorum;
        $this->integromat = $integromat;
        $this->spallian = $spallian;
    }

    public function persist(ContactApiData $data, Project|Organization $in): Contact
    {
        $inOrganization = $in instanceof Project ? $in->getOrganization() : $in;

        // Search for an existing contact or create a new one and apply payload
        $created = false;
        if (!$data->email || !$contact = $this->repository->findOneByAnyEmail($inOrganization, $data->email)) {
            $contact = new Contact($inOrganization, $data->email);
            $created = true;
        }

        // Map the data and check whether the contact became a member / just received new tags
        $wasMember = $contact->isMember();
        $previousTagsNames = $contact->getMetadataTagsNames();

        $this->map($contact, $data, ($created && $in instanceof Project) ? $in : null);

        // If the contact has just been created, trigger automations
        if ($created) {
            $this->automationDispatcher->dispatch(EmailAutomation::TRIGGER_NEW_CONTACT, $contact->getOrganization(), $contact, null);
        }

        // Trigger automations for newly added tags
        foreach ($contact->getMetadataTags() as $tag) {
            if (!in_array($tag->getName(), $previousTagsNames, true)) {
                $this->automationDispatcher->dispatch(EmailAutomation::TRIGGER_CONTACT_TAGGED, $contact->getOrganization(), $contact, $tag);
            }
        }

        // If the contact just became a member, start validation process
        if ($in instanceof Project && !$wasMember && $contact->isMember()) {
            $this->organizationMailer->sendRegistrationConfirm($in, $contact);
        }

        // Update CRM search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        // Finally, trigger integrations
        if ($created) {
            $this->integromat->triggerWebhooks($contact);
        }

        if ('qomon' !== $contact->getMetadataSource()) {
            $this->quorum->persist($contact);
        }

        $this->spallian->persist($contact, $created);

        return $contact;
    }

    public function updateImage(Contact $contact, Upload $picture): Contact
    {
        $contact->setPicture($picture);

        $this->em->persist($contact);
        $this->em->flush();

        // Update CRM search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        // Trigger integrations
        $this->quorum->persist($contact);
        $this->spallian->persist($contact, false);

        return $contact;
    }

    private function map(Contact $contact, ContactApiData $data, ?Project $sourceProject = null)
    {
        // Add source project tag
        if ($sourceProject) {
            $data->metadataTags[] = $sourceProject->getName();
        }

        // Find contact country
        if ($data->addressCountry && $country = $this->locator->findContactCountry($data->addressCountry)) {
            $data->addressCountry = $country->getCode();
            $contact->setCountry($country);
        }

        if ($data->profileNationality) {
            $data->profileNationality = strtoupper($data->profileNationality);
        }

        // Apply update
        $contact->applyApiUpdate($data, 'api');

        // Set recruitedBy after base update (if provided email)
        if ($data->recruitedBy) {
            if ($recruiter = $this->repository->findOneByAnyEmail($contact->getOrganization(), $data->recruitedBy)) {
                $contact->setRecruitedBy($recruiter);
            }
        }

        // Locate the contact if possible
        $contact->setArea($this->locator->findContactArea($contact));

        // Create the contact as member if requested
        if ($data->accountPassword && !$contact->isMember()) {
            $contact->changePassword($this->hasher->hashPassword($contact, $data->accountPassword));
            $contact->startAccountConfirmProcess();
        }

        // Persist a first time before synchronizing tags
        $this->em->persist($contact);
        $this->em->flush();

        if ($data->metadataTags || $data->metadataTagsOverride) {
            // Resolve new tags list
            $previousTags = $contact->getMetadataTagsNames();
            if ($data->metadataTagsOverride) {
                $previousTags = $data->metadataTagsOverride;
            }

            $resolvedNewTags = array_unique(array_merge($previousTags, $data->metadataTags ?: []));

            // Replace current tags with new ones
            $this->em->getRepository(Tag::class)->replaceContactTags($contact, $resolvedNewTags);
            $this->em->refresh($contact);
        }

        // Mandates override if provided (null means ignore)
        if (null !== $data->mandates) {
            // Remove existing
            foreach ($contact->getMandates() as $mandate) {
                $this->em->remove($mandate);
            }
            // Also clear owning side collection in memory
            $contact->getMandates()->clear();

            // Recreate from payload
            foreach ((array) $data->mandates as $m) {
                $label = (string) ($m['label'] ?? '');
                if (!$label) {
                    continue;
                }

                $startAt = null;
                $endAt = null;
                try {
                    if (!empty($m['startAt'])) {
                        $startAt = new \DateTimeImmutable((string) $m['startAt']);
                    }
                } catch (\Exception) {
                }
                try {
                    if (!empty($m['endAt'])) {
                        $endAt = new \DateTimeImmutable((string) $m['endAt']);
                    }
                } catch (\Exception) {
                }

                if (!$startAt || !$endAt) {
                    // Skip invalid date ranges
                    continue;
                }

                $mandate = new ContactMandate(
                    $contact,
                    ContactMandateType::Internal,
                    $label,
                    $startAt,
                    $endAt,
                );
                $this->em->persist($mandate);
                $contact->getMandates()->add($mandate);
            }

            $this->em->flush();
        }

        // Commitments override if provided (null means ignore)
        if (null !== $data->commitments) {
            // Remove existing
            foreach ($contact->getCommitments() as $commitment) {
                $this->em->remove($commitment);
            }
            $contact->getCommitments()->clear();

            // Recreate from payload
            foreach ((array) $data->commitments as $c) {
                $label = (string) ($c['label'] ?? '');
                if (!$label) {
                    continue;
                }

                $commitment = new ContactCommitment($contact);
                $startAt = null;
                try {
                    if (!empty($c['startAt'])) {
                        $startAt = new \DateTimeImmutable((string) $c['startAt']);
                    }
                } catch (\Exception) {
                }
                $commitment->setLabel($label);
                $commitment->setStartAt($startAt);
                $this->em->persist($commitment);
                $contact->getCommitments()->add($commitment);
            }

            $this->em->flush();
        }
    }
}
