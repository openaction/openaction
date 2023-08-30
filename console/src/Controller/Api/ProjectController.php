<?php

namespace App\Controller\Api;

use App\Api\Transformer\ProjectTransformer;
use App\Api\Transformer\SitemapTransformer;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'General')]
#[Route('/api/project')]
class ProjectController extends AbstractApiController
{
    /**
     * Get the current project details.
     *
     * Available includes: header, footer, home.
     */
    #[Route('', name: 'api_project', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the details of the currently authenticated project.',
        content: new OA\JsonContent(ref: '#/components/schemas/Project'),
    )]
    public function project(ProjectTransformer $transformer)
    {
        return $this->handleApiItem($this->getUser(), $transformer);
    }

    /**
     * Get the current project sitemap.
     *
     * Returns the necessary details to generate a sitemap for the current project.
     */
    #[Route('/sitemap', name: 'api_project_sitemap', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the sitemap details.',
        content: new OA\JsonContent(ref: '#/components/schemas/Sitemap'),
    )]
    public function sitemap(SitemapTransformer $transformer)
    {
        return $this->handleApiItem($this->getUser(), $transformer);
    }
}
