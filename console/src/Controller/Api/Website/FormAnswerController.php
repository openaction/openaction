<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\FormAnswerTransformer;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Repository\Website\FormAnswerRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class FormAnswerController extends AbstractApiController
{
    use ApiControllerTrait;

    public function __construct(private FormAnswerRepository $repository)
    {
    }

    #[Route('/forms-answers/{id}', name: 'api_website_forms_answers_view', methods: ['GET'])]
    public function view(FormAnswerTransformer $transformer, string $id)
    {
        if (!$answer = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($answer->getForm());

        if ($answer->getForm()->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($answer, $transformer);
    }
}
