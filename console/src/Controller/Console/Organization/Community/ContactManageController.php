<?php

namespace App\Controller\Console\Organization\Community;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Bridge\Integromat\IntegromatInterface;
use App\Bridge\Quorum\QuorumInterface;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\Automation\EmailAutomationDispatcher;
use App\Community\ContactLocator;
use App\Community\History\ContactHistoryBuilder;
use App\Controller\AbstractController;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Form\Community\ContactType;
use App\Form\Community\Model\ContactData;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/community/contacts')]
class ContactManageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContactLocator $locator,
        private ContactRepository $contactRepository,
        private MessageBusInterface $bus,
        private QuorumInterface $quorum,
        private IntegromatInterface $integromat
    ) {
    }

    #[Route('/{uuid}/view', name: 'console_organization_community_contacts_view')]
    public function view(ContactHistoryBuilder $historyBuilder, Contact $contact)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($contact);

        return $this->render('console/organization/community/contacts/view.html.twig', [
            'contact' => $contact,
            'history' => $historyBuilder->buildHistory($contact),
        ]);
    }

    #[Route('/{uuid}/history', name: 'console_organization_community_contacts_history')]
    public function history(ContactHistoryBuilder $historyBuilder, Contact $contact)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($contact);

        return $this->render('console/organization/community/contacts/history.html.twig', [
            'organization' => $this->getOrganization(),
            'contact' => $contact,
            'history' => $historyBuilder->buildHistory($contact),
        ]);
    }

    #[Route('/create', name: 'console_organization_community_contacts_create')]
    public function create(CdnUploader $uploader, EmailAutomationDispatcher $automationDispatcher, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $data = new ContactData();

        $form = $this->createForm(ContactType::class, $data, [
            'allow_edit_tags' => $this->getOrganization()->isFeatureInPlan(Features::FEATURE_COMMUNITY_EMAILING_TAGS),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create the contact
            $contact = new Contact($this->getOrganization(), $data->email);
            $contact->applyDataUpdate($data, 'console:orga-create');
            $contact->setArea($this->locator->findContactArea($contact));

            // Apply picture update
            if ($data->picture) {
                $contact->setPicture($uploader->upload(CdnUploadRequest::createContactPictureRequest($data->picture)));
            }

            $this->em->persist($contact);
            $this->em->flush();

            $this->contactRepository->updateTags($contact, $data->parseTags());

            // Update CRM search index
            $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

            // Trigger automations
            $automationDispatcher->dispatch(EmailAutomation::TRIGGER_NEW_CONTACT, $contact->getOrganization(), $contact, null);

            // Integrations
            $this->integromat->triggerWebhooks($contact);
            $this->quorum->persist($contact);

            // Compute the stats
            $this->bus->dispatch(new RefreshContactStatsMessage($contact->getOrganization()->getId()));

            // Redirect to edition
            $this->addFlash('success', 'contacts.created_success');

            return $this->redirectToRoute('console_organization_community_contacts_edit', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $contact->getUuid(),
            ]);
        }

        return $this->render('console/organization/community/contacts/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_organization_community_contacts_edit')]
    public function edit(CdnUploader $uploader, MessageBusInterface $bus, Contact $contact, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($contact);

        $this->contactRepository->refreshContactSettings($contact);

        $data = ContactData::createFromContact($contact);

        $form = $this->createForm(ContactType::class, $data, [
            'allow_edit_tags' => $this->getOrganization()->isFeatureInPlan(Features::FEATURE_COMMUNITY_EMAILING_TAGS),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Apply picture update
            if ($data->picture) {
                $contact->setPicture($uploader->upload(CdnUploadRequest::createContactPictureRequest($data->picture)));
            }

            // Apply data updates
            $contact->applyDataUpdate($data, 'console:orga-edit');
            $contact->setArea($this->locator->findContactArea($contact));

            $this->em->persist($contact);
            $this->em->flush();

            // Update CRM search index
            $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

            // Trigger automations
            $this->contactRepository->updateTags($contact, $data->parseTags());

            // Sync Quorum
            $this->quorum->persist($contact);

            // Compute the stats
            $bus->dispatch(new RefreshContactStatsMessage($contact->getOrganization()->getId()));

            // Redirect to edition
            $this->addFlash('success', 'contacts.updated_success');

            return $this->redirectToRoute('console_organization_community_contacts_edit', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $contact->getUuid(),
            ]);
        }

        return $this->render('console/organization/community/contacts/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'form_is_invalid' => $form->isSubmitted() && !$form->isValid(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_organization_community_contacts_delete')]
    public function delete(Contact $contact, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($contact);

        $this->em->remove($contact);
        $this->em->flush();

        // Delete from CRM search index
        $this->bus->dispatch(new RemoveCrmDocumentMessage($this->getOrganization()->getId(), $contact->getUuid()->toRfc4122()));

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_organization_community_contacts', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
