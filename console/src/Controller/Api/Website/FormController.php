<?php

namespace App\Controller\Api\Website;

use App\Api\Model\FormAnswerApiData;
use App\Api\Persister\FormAnswerApiPersister;
use App\Api\Transformer\Website\FormFullTransformer;
use App\Api\Transformer\Website\FormPartialTransformer;
use App\Community\Member\AuthorizationToken;
use App\Community\MemberAuthenticator;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Repository\Website\FormRepository;
use App\Util\Json;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class FormController extends AbstractApiController
{
    use ApiControllerTrait;

    public function __construct(private FormRepository $repository, private MemberAuthenticator $memberAuthenticator)
    {
    }

    #[Route('/forms', name: 'api_website_forms_list', methods: ['GET'])]
    public function list(FormPartialTransformer $transformer)
    {
        return $this->handleApiCollection($this->repository->getApiForms($this->getUser()), $transformer, false);
    }

    #[Route('/forms/{id}', name: 'api_website_forms_view', methods: ['GET'])]
    public function view(FormFullTransformer $transformer, string $id)
    {
        if (!$form = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($form);

        if ($form->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($form, $transformer);
    }

    #[Route('/forms/{id}/answer', name: 'api_website_forms_answer', methods: ['POST'])]
    public function answer(ValidatorInterface $validator, FormAnswerApiPersister $persister, Request $request, string $id)
    {
        if (!$form = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($form);

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        $data = FormAnswerApiData::createFromPayload($payload);

        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        // If the current member is logged in, link it as contact
        $linkedContact = null;
        try {
            $token = AuthorizationToken::createFromPayload(Json::decode($request->headers->get(MemberAuthenticator::TOKEN_HEADER)) ?? []);
            $linkedContact = $this->memberAuthenticator->authorize($token);
        } catch (\Exception) {
        }

        $persister->persist($form, $data, $linkedContact);

        return $this->createJsonApiResponse('OK', Response::HTTP_CREATED);
    }
}
