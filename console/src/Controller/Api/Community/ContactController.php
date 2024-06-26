<?php

namespace App\Controller\Api\Community;

use App\Api\Model\ContactApiData;
use App\Api\Model\ContactListApiData;
use App\Api\Model\ContactPictureApiData;
use App\Api\Model\ContactUpdateEmailApiData;
use App\Api\Persister\ContactApiPersister;
use App\Api\Transformer\Community\ContactTransformer;
use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\ContactViewBuilder;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactUpdate;
use App\Entity\Project;
use App\Mailer\OrganizationMailer;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ContactUpdateRepository;
use App\Repository\Platform\LogRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Util\Json;
use App\Util\Uid;
use App\Validator\ContactUpdateToken;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Community')]
#[Route('/api/community/contacts')]
class ContactController extends AbstractApiController
{
    use ApiControllerTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ContactRepository $repository,
        private readonly ContactUpdateRepository $contactUpdateRepository,
        private readonly OrganizationMailer $mailer,
        private readonly ContactTransformer $transformer,
        private readonly MessageBusInterface $bus,
    ) {
    }

    /**
     * Get the list of contacts of the current project.
     *
     * The contacts of a project can be all contacts from the organization if the project
     * is global or only a segment of contacts if the project is local or thematic.
     *
     * This endpoint is paginated.
     */
    #[Route('', name: 'api_contacts_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of contacts in the current project.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Contact'))
    )]
    public function list(ContactViewBuilder $viewBuilder, Request $request)
    {
        $data = ContactListApiData::createFromPayload($request->query->all());

        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        $currentPage = $this->apiQueryParser->getPage();

        $contacts = $viewBuilder
            ->inProject($this->getUser())
            ->onlyMembers($data->only_members)
            ->inAreas($data->areas_filter)
            ->withTags($data->tags_filter, $data->tags_filter_type)
            ->setPage($currentPage, 50)
            ->paginate()
        ;

        return $this->handleApiCollection($contacts, $this->transformer, true);
    }

    /**
     * Search a contact in the organization index.
     */
    #[Route('/search', name: 'api_contacts_search', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the contacts results of the search.',
        content: new OA\JsonContent(type: 'array'),
    )]
    public function search(MeilisearchInterface $meilisearch, Request $request)
    {
        /** @var Project $project */
        $project = $this->getUser();

        try {
            $payload = Json::decode($request->getContent());
        } catch (\Exception) {
            $payload = [];
        }

        $payload['filter'][] = 'projects = "'.$project->getUuid()->toRfc4122().'"';

        return new JsonResponse($meilisearch->search(
            index: $project->getOrganization()->getCrmIndexName(),
            query: $payload['q'] ?? null,
            searchParams: $payload,
        ));
    }

    /**
     * Create or update a contact profile.
     *
     * Use this endpoint to create or update contacts in your organization.
     *
     * This call uses the email as a key: if the email already exists in the organization's contacts database,
     * the profile will be updated. Otherwise it will be created.
     */
    #[Route('', name: 'api_contacts_create_update', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the persisted contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function persist(LogRepository $logRepository, ContactApiPersister $persister, Request $request)
    {
        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        $data = ContactApiData::createFromPayload($payload);

        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        // Log payload for debugging
        $logRepository->createLog('api_contacts_create_update', $payload);

        $contact = $persister->persist($data, $this->getUser());

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Update email of a contact.
     *
     * Use this endpoint to update the contact email in your organization.
     */
    #[Route('/{id}/email', name: 'api_contacts_update_email', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the persisted contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function updateEmail(Request $request, string $id)
    {
        /** @var Contact $contact */
        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException('Contact not found');
        }

        $this->denyUnlessSameOrganization($contact);

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        $data = ContactUpdateEmailApiData::createFromPayload($payload, $contact);

        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        if ($data->newEmail && $this->repository->hasContactInOrganization($data->newEmail, $contact)) {
            throw new UnprocessableEntityHttpException('This email is already used in your organization.');
        }

        if (!$contact->isMember()) {
            $this->repository->updateEmail($data);

            // Update CRM search index
            $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));
        } elseif (!$data->newEmail) {
            throw new UnprocessableEntityHttpException('This contact is a member, it requires a valid email address.');
        } else {
            $this->mailer->sendEmailChangedConfirm(
                $this->getUser(),
                $this->contactUpdateRepository->createContactEmailUpdate($data)
            );
        }

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Unregister contact.
     */
    #[Route('/{id}/unregister', name: 'api_contacts_update_unregister', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the contact updated.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function unregister(string $id)
    {
        /** @var Contact $contact */
        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException('Contact not found');
        }

        $this->denyUnlessSameOrganization($contact);

        if (!$contact->isMember()) {
            $this->repository->unregister($contact);

            // Update CRM search index
            $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));
        } else {
            $this->mailer->sendUnregisterConfirm(
                $this->getUser(),
                $this->contactUpdateRepository->createContactUnregisterUpdate($contact)
            );
        }

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Try to confirm the update in the email of a member by checking the sent token.
     */
    #[Route('/confirm/{id}/{token}/email', name: 'api_contacts_update_email_confirm', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the confirmed contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function updateEmailConfirm(string $id, string $token)
    {
        $contactUpdate = $this->validateContactUpdate($id, $token, ContactUpdate::TYPE_EMAIL);
        $contactUpdate->getContact()->changeEmail($contactUpdate->getEmail());

        $contact = $this->contactUpdateRepository->apply($contactUpdate);

        // Update CRM search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Try to confirm unregister contact in the email of a member by checking the sent token.
     */
    #[Route('/confirm/{id}/{token}/unregister', name: 'api_contacts_unregister_confirm', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the confirmed contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function unregisterConfirm(string $id, string $token)
    {
        $contactUpdate = $this->validateContactUpdate($id, $token, ContactUpdate::TYPE_UNREGISTER);
        $contactUpdate->getContact()->applyUnregister();

        $contact = $this->contactUpdateRepository->apply($contactUpdate);

        // Update CRM search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Check the status of the given email in the current project's organization.
     */
    #[Route('/status/{email}', requirements: ['email' => '.+'], name: 'api_contacts_status', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the status.',
        content: new OA\JsonContent(
            title: 'ContactStatus',
            properties: [
                new OA\Property(property: '_resource', type: 'string'),
                new OA\Property(property: 'status', type: 'string', enum: ['member', 'contact', 'not_found']),
                new OA\Property(property: 'id', type: 'string', nullable: true),
            ]
        )
    )]
    public function status(Request $request, string $email)
    {
        $contact = $this->repository->findOneByAnyEmail(
            $this->getUser()?->getOrganization(),
            $email,
            onlyMainEmail: $request->query->getBoolean('onlyMainEmail'),
        );

        if (!$contact) {
            return new JsonResponse(['_resource' => 'ContactStatus', 'status' => 'not_found', 'id' => null, 'tags' => []]);
        }

        return new JsonResponse([
            '_resource' => 'ContactStatus',
            'status' => $contact->isMember() ? 'member' : 'contact',
            'tags' => $contact->getMetadataTagsNames(),
            'id' => Uid::toBase62($contact->getUuid()),
        ]);
    }

    /**
     * Get a contact details.
     */
    #[Route('/{id}', name: 'api_contacts_view', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function view(string $id)
    {
        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameOrganization($contact);

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Update a contact picture.
     *
     * Use this endpoint to update the picture of contacts in your organization.
     *
     * The picture file should be sent as part of a multipart/form-data request, under
     * a "picture" field.
     */
    #[Route('/{id}/picture', name: 'api_contacts_update_picture', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Returns 200 when the picture was successfully updated.'
    )]
    public function updatePicture(CdnUploader $uploader, ContactApiPersister $persister, Request $request, string $id)
    {
        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameOrganization($contact);

        $data = new ContactPictureApiData($request->files->get('picture'));

        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        $persister->updateImage(
            $contact,
            $uploader->upload(CdnUploadRequest::createContactPictureRequest($data->picture))
        );

        return new JsonResponse([]);
    }

    private function validateContactUpdate(string $id, string $token, string $type): ContactUpdate
    {
        /** @var ContactUpdate $contactUpdate */
        if (!$contactUpdate = $this->contactUpdateRepository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameOrganization($contactUpdate->getContact());

        if ($type !== $contactUpdate->getType()) {
            throw $this->createAccessDeniedException();
        }

        $errors = $this->validator->validate($contactUpdate, new ContactUpdateToken(token: $token));
        if ($errors->count() > 0) {
            throw $this->createAccessDeniedException();
        }

        return $contactUpdate;
    }
}
