<?php

namespace App\Controller\Console\Project\Community;

use App\Bridge\Quorum\QuorumInterface;
use App\Community\ContactLocator;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\History\ContactHistoryBuilder;
use App\Controller\AbstractController;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactCommitment;
use App\Entity\Community\ContactMandate;
use App\Entity\Community\Enum\ContactMandateType;
use App\Form\Community\ContactType;
use App\Form\Community\Model\ContactData;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Api\Transformer\Community\ContactTransformer;

#[Route('/console/project/{projectUuid}/community/contacts')]
class ContactManageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContactRepository $repository,
        private QuorumInterface $quorum,
        private MessageBusInterface $bus,
    ) {
    }

    #[Route('/{uuid}/view', name: 'console_community_contacts_view')]
    public function view(ContactHistoryBuilder $historyBuilder, Contact $contact)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_VIEW, $this->getProject());
        $this->denyIfSubscriptionExpired();

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createNotFoundException();
        }

        return $this->render('console/project/community/contacts/view.html.twig', [
            'contact' => $contact,
            'history' => $historyBuilder->buildHistory($contact),
        ]);
    }

    #[Route('/{uuid}/history', name: 'console_community_contacts_history')]
    public function history(ContactHistoryBuilder $historyBuilder, Contact $contact)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_VIEW, $this->getProject());
        $this->denyIfSubscriptionExpired();

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createNotFoundException();
        }

        return $this->render('console/project/community/contacts/history.html.twig', [
            'project' => $this->getProject(),
            'contact' => $contact,
            'history' => $historyBuilder->buildHistory($contact),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_community_contacts_edit')]
    public function edit(CdnUploader $uploader, MessageBusInterface $bus, Contact $contact, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_UPDATE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createNotFoundException();
        }

        $data = ContactData::createFromContact($contact);

        $form = $this->createForm(ContactType::class, $data, [
            'allow_edit_tags' => $this->getOrganization()->isFeatureInPlan(Features::FEATURE_COMMUNITY_EMAILING_TAGS),
            'allow_edit_area' => false,
            'allow_edit_settings' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Apply picture update
            if ($data->picture) {
                $contact->setPicture($uploader->upload(CdnUploadRequest::createContactPictureRequest($data->picture)));
            }

            // Apply data updates
            $contact->applyDataUpdate($data, 'console:project-edit');

            $this->em->persist($contact);
            $this->em->flush();

            // Recruited by
            if ($data->recruitedBy) {
                $contact->setRecruitedBy($data->recruitedBy);
            } else {
                $contact->setRecruitedBy(null);
            }

            // Mandates (replace all)
            if (null !== $data->mandates) {
                foreach ($contact->getMandates() as $m) {
                    $this->em->remove($m);
                }
                $contact->getMandates()->clear();

                foreach ((array) $data->mandates as $m) {
                    $label = (string) ($m['label'] ?? '');
                    $type = $m['type'] ?? null;
                    $startAt = $m['startAt'] ?? null;
                    $endAt = $m['endAt'] ?? null;
                    if (!$label || !$type || !$startAt || !$endAt) {
                        continue;
                    }
                    if (!$type instanceof ContactMandateType) {
                        try {
                            $type = ContactMandateType::from((string) $type);
                        } catch (\Throwable) {
                            continue;
                        }
                    }
                    try {
                        $start = new \DateTimeImmutable((string) $startAt);
                        $end = new \DateTimeImmutable((string) $endAt);
                    } catch (\Exception) {
                        continue;
                    }
                    $mandate = new ContactMandate($contact, $type, $label, $start, $end);
                    $this->em->persist($mandate);
                    $contact->getMandates()->add($mandate);
                }
            }

            // Commitments (replace all)
            if (null !== $data->commitments) {
                foreach ($contact->getCommitments() as $c) {
                    $this->em->remove($c);
                }
                $contact->getCommitments()->clear();

                foreach ((array) $data->commitments as $c) {
                    $label = (string) ($c['label'] ?? '');
                    if (!$label) {
                        continue;
                    }
                    $commitment = new ContactCommitment($contact);
                    $commitment->setLabel($label);
                    $start = null;
                    if (!empty($c['startAt'])) {
                        try {
                            $start = new \DateTimeImmutable((string) $c['startAt']);
                        } catch (\Exception) {
                            $start = null;
                        }
                    }
                    $commitment->setStartAt($start);
                    $this->em->persist($commitment);
                    $contact->getCommitments()->add($commitment);
                }
            }

            $this->em->flush();

            $this->repository->updateTags($contact, $data->parseTags());

            // Update CRM search index
            $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

            // Sync Quorum
            $this->quorum->persist($contact);

            // Redirect to edition
            $this->addFlash('success', 'contacts.updated_success');

            return $this->redirectToRoute('console_community_contacts_edit', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $contact->getUuid(),
            ]);
        }

        return $this->render('console/project/community/contacts/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_community_contacts_delete')]
    public function delete(Contact $contact, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_DELETE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createNotFoundException();
        }

        $this->em->remove($contact);
        $this->em->flush();

        // Delete from CRM search index
        $this->bus->dispatch(new RemoveCrmDocumentMessage($contact->getOrganization()->getId(), $contact->getUuid()->toRfc4122()));

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_contacts', [
            'projectUuid' => $this->getProject()->getUuid(),
        ]);
    }

    /**
     * Get contact data as JSON for the drawer UI.
     */
    #[Route('/{uuid}/data', name: 'console_community_contacts_data', methods: ['GET'])]
    public function getContactJson(Contact $contact, ContactTransformer $transformer): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_VIEW, $this->getProject());
        $this->denyIfSubscriptionExpired();

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createNotFoundException();
        }

        // Use the transformer - settings WILL be included as per user request
        return new JsonResponse($transformer->transform($contact));
    }

    /**
     * Update contact data from the drawer UI.
     */
    #[Route('/{uuid}', name: 'console_community_contacts_update', methods: ['PATCH'])]
    public function updateContactJson(Request $request, Contact $contact, CdnUploader $uploader, ContactLocator $locator): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_UPDATE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createNotFoundException();
        }

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->json(['error' => 'Invalid JSON provided'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = ContactData::createFromContact($contact);

        $form = $this->createForm(ContactType::class, $data, [
            'allow_edit_tags' => $this->getOrganization()->isFeatureInPlan(Features::FEATURE_COMMUNITY_EMAILING_TAGS),
            'allow_edit_area' => false, // Project view disallows editing area
            'allow_edit_settings' => false, // Project view disallows editing settings
            'method' => 'PATCH',
            'csrf_protection' => false, // Disabled for JSON API endpoint test
        ]);

        $form->submit($payload, false); // false for partial updates

        if (!$form->isValid()) {
            return $this->json(['errors' => $this->getFormErrors($form)], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // The ContactData object $data now holds the submitted and validated values
        $contact->applyDataUpdate($data); // Removed source argument
        // Area updates are disallowed in the project context form config, so no need to call setArea/locator here

        $this->em->persist($contact);
        $this->em->flush();

        // Update tags based on ContactData
        $this->repository->updateTags($contact, $data->parseTags());

        // Update CRM search index, sync quorum
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));
        $this->quorum->persist($contact); // Quorum sync might still be relevant in project context

        return $this->json(['success' => true, 'contact_uuid' => $contact->getUuid()->toRfc4122()]);
    }
}
