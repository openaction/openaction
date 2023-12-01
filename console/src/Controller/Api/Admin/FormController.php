<?php

namespace App\Controller\Api\Admin;

use App\Api\Transformer\Website\FormAnswerTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\FormAnswerRepository;
use App\Repository\Website\FormRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admin')]
#[Route('/api/admin')]
class FormController extends AbstractApiController
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormAnswerRepository $repository,
        private readonly FormAnswerTransformer $transformer,
    ) {
    }

    #[Route('/forms/{id}/answers', name: 'api_admin_forms_answers_list', methods: ['GET'])]
    public function listAnswers(string $id): Response
    {
        if (!$form = $this->formRepository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $currentPage = $this->apiQueryParser->getPage();
        $answers = $this->repository->getPaginator($form, $currentPage);

        return $this->handleApiCollection($answers, $this->transformer, true);
    }
}
