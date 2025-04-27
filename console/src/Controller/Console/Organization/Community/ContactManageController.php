<?php

namespace App\Controller\Console\Organization\Community;

use App\Bridge\Integromat\IntegromatInterface;
use App\Bridge\Quorum\QuorumInterface;
use App\Cdn\CdnUploader;
use App\Cdn\CdnRouter;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\Automation\EmailAutomationDispatcher;
use App\Community\ContactLocator;
use App\Community\History\ContactHistoryBuilder;
use App\Controller\AbstractController;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Form\Community\ContactType;
use App\Form\Community\ContactPictureType;
use App\Form\Community\Model\ContactData;
use App\Form\Community\Model\ContactPictureData;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Util\Json;
use App\Api\Transformer\Community\ContactTransformer;

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

        $data = new ContactData($this->getOrganization());

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

    /**
     * Get contact data as JSON for the drawer UI.
     */
    #[Route('/{uuid}/data', name: 'console_organization_community_contacts_data', methods: ['GET'])]
    public function getContactJson(Contact $contact, ContactTransformer $transformer): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessSameOrganization($contact);

        // Refresh settings before serializing
        $this->contactRepository->refreshContactSettings($contact);

        return new JsonResponse($transformer->transform($contact));
    }

    /**
     * Update contact data from the drawer UI.
     */
    #[Route('/{uuid}', name: 'console_organization_community_contacts_update', methods: ['PATCH'])]
    public function updateContactJson(Request $request, Contact $contact, CdnUploader $uploader): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameOrganization($contact);

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->json(['error' => 'Invalid JSON provided'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = ContactData::createFromContact($contact);

        $form = $this->createForm(ContactType::class, $data, [
            'allow_edit_tags' => $this->getOrganization()->isFeatureInPlan(Features::FEATURE_COMMUNITY_EMAILING_TAGS),
            'allow_edit_area' => true,
            'allow_edit_settings' => true,
            'method' => 'PATCH',
            'csrf_protection' => false,
        ]);

        $form->submit($payload, false); // false for partial updates

        if (!$form->isValid()) {
            // Return form errors in a structured way for the frontend
            return $this->json(['errors' => $this->getFormErrors($form)], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // The ContactData object $data now holds the submitted and validated values
        // The ContactType form maps JSON keys to ContactData properties
        $contact->applyDataUpdate($data); // Removed source argument
        $contact->setArea($this->locator->findContactArea($contact));

        $this->em->persist($contact);
        $this->em->flush();

        $this->contactRepository->updateTags($contact, $data->parseTags());

        // Update CRM search index, trigger integrations
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));
        $this->quorum->persist($contact);
        $this->integromat->triggerWebhooks($contact);

        return $this->json(['success' => true, 'contact_uuid' => $contact->getUuid()->toRfc4122()]);
    }

    #[Route('/{uuid}/picture', name: 'console_organization_community_contacts_update_picture', methods: ['POST'])]
    public function updatePicture(Request $request, Contact $contact, CdnUploader $uploader, CdnRouter $cdnRouter): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessSameOrganization($contact);
        // No CSRF check needed if using sessionless auth or if framework handles it based on config
        // However, ensure proper authentication and authorization are in place.

        $data = new ContactPictureData();
        $form = $this->createForm(ContactPictureType::class, $data);
        $form->handleRequest($request); // Handles the uploaded file

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Upload new picture and update contact
        $cdnAsset = $uploader->upload(CdnUploadRequest::createContactPictureRequest($data->file));
        $contact->setPicture($cdnAsset);

        $this->em->persist($contact);
        $this->em->flush();

        // Update search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        return $this->json(['success' => true, 'picture_url' => $cdnRouter->generateUrl($cdnAsset)]);
    }
}
