<?php

namespace App\Api\Persister;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Api\Model\ContactApiData;
use App\Bridge\Integromat\IntegromatInterface;
use App\Bridge\Quorum\QuorumInterface;
use App\Bridge\Spallian\SpallianInterface;
use App\Community\Automation\EmailAutomationDispatcher;
use App\Community\ContactLocator;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
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
        SpallianInterface $spallian
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

    public function persist(ContactApiData $data, Project|Organization $in, bool $computeStats = true): Contact
    {
        $inOrganization = $in instanceof Project ? $in->getOrganization() : $in;

        // Search for an existing contact or create a new one and apply payload
        $created = false;
        if (!$data->email || !$contact = $this->repository->findOneByAnyEmail($inOrganization, $data->email)) {
            $contact = new Contact($inOrganization, $data->email);
            $created = true;
        }

        // Map the data and check whether the contact became a member
        $wasMember = $contact->isMember();
        $this->map($contact, $data, ($created && $in instanceof Project) ? $in : null);

        // If the contact has just been created, trigger automations
        if ($created) {
            $this->automationDispatcher->dispatch(EmailAutomation::TRIGGER_NEW_CONTACT, $contact->getOrganization(), $contact, null);
        }

        // If the contact just became a member, start validation process
        if ($in instanceof Project && !$wasMember && $contact->isMember()) {
            $this->organizationMailer->sendRegistrationConfirm($in, $contact);
        }

        // Compute the stats again
        if ($computeStats) {
            $this->bus->dispatch(new RefreshContactStatsMessage($contact->getOrganization()->getId()));
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

    private function map(Contact $contact, ContactApiData $data, Project $sourceProject = null)
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

        if (!$data->metadataTags && !$data->metadataTagsOverride) {
            return;
        }

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
}
