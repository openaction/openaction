<?php

namespace App\Controller\Api\Community;

use App\Api\Transformer\Community\TagTransformer;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Repository\Community\TagRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Community')]
#[Route('/api/community/tags')]
class TagController extends AbstractApiController
{
    use ApiControllerTrait;

    /**
     * Get a contact details.
     */
    #[Route('', name: 'api_contacts_tags', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the contact details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Tag')
    )]
    public function tags(TagRepository $repo, TagTransformer $transformer)
    {
        return $this->handleApiCollection(
            $repo->findAllByOrganization($this->getUser()->getOrganization()),
            $transformer,
            false
        );
    }
}
