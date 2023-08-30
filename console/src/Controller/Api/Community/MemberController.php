<?php

namespace App\Controller\Api\Community;

use App\Api\Model\LoginApiData;
use App\Api\Transformer\Community\AuthorizationTokenTransformer;
use App\Api\Transformer\Community\ContactTransformer;
use App\Community\Member\AuthorizationToken;
use App\Community\MemberAuthenticator;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Mailer\OrganizationMailer;
use App\Platform\Features;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Membership')]
#[Route('/api/community/members')]
class MemberController extends AbstractApiController
{
    use ApiControllerTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private ContactRepository $repository,
        private ContactTransformer $transformer,
        private MemberAuthenticator $authenticator,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * Try to confirm the email of a member by checking the sent token.
     */
    #[Route('/register/confirm/{id}/{token}', name: 'api_members_register_confirm', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the confirmed contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function registerConfirm(string $id, string $token)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);

        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        if (!$contact->confirmAccount($token)) {
            throw $this->createNotFoundException();
        }

        $this->em->persist($contact);
        $this->em->flush();

        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Try to log in a member with an email and a password.
     *
     * If successful, an AuthorizationToken is created that can then be used to authorize future
     * Membership API calls.
     */
    #[Route('/login', name: 'api_members_login', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the authorization token.',
        content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationToken')
    )]
    public function login(ValidatorInterface $validator, AuthorizationTokenTransformer $transformer, Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        $data = LoginApiData::createFromPayload($payload);

        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        if (!$contact = $this->authenticator->authenticate($this->getUser()->getOrganization(), $data)) {
            return $this->createJsonApiProblemResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        return $this->handleApiItem($this->authenticator->createAuthorizationToken($contact), $transformer);
    }

    /**
     * Check whether the given AuthorizationToken is valid.
     */
    #[Route('/authorize', name: 'api_members_authorize', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the logged in contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function authorize(Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);

        try {
            $token = AuthorizationToken::createFromPayload(Json::decode($request->headers->get(MemberAuthenticator::TOKEN_HEADER)) ?? []);
        } catch (\Exception) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        if (!$contact = $this->authenticator->authorize($token)) {
            return $this->createJsonApiProblemResponse('Invalid authorization token', Response::HTTP_UNAUTHORIZED);
        }

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Request a password reset email.
     *
     * This endpoint will send an email to the member with a link to reset their password.
     */
    #[Route('/reset/request/{id}', name: 'api_members_reset_request', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function reset(OrganizationMailer $mailer, string $id)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);

        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameOrganization($contact);

        if (!$contact->startAccountResetProcess()) {
            throw $this->createNotFoundException();
        }

        $this->em->persist($contact);
        $this->em->flush();

        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        $mailer->sendResetConfirm($this->getUser(), $contact);

        return $this->handleApiItem($contact, $this->transformer);
    }

    /**
     * Confirm a password reset email.
     *
     * Updates the member's password.
     */
    #[Route('/reset/confirm/{id}/{token}', name: 'api_members_reset_confirm', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Return the contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Contact')
    )]
    public function resetConfirm(UserPasswordHasherInterface $hasher, string $id, string $token, Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);

        if (!$contact = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        try {
            $payload = Json::decode($request->getContent());
        } catch (\Exception) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['password'])) {
            return $this->createJsonApiProblemResponse('Invalid payload: missing password', Response::HTTP_BAD_REQUEST);
        }

        $this->denyUnlessSameOrganization($contact);

        if (!$contact->resetAccount($token, $hasher->hashPassword($contact, $payload['password']))) {
            throw $this->createNotFoundException();
        }

        $this->em->persist($contact);
        $this->em->flush();

        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        return $this->handleApiItem($contact, $this->transformer);
    }
}
