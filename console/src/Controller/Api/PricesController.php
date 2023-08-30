<?php

namespace App\Controller\Api;

use App\Api\Transformer\PricesTransformer;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Prices')]
class PricesController extends AbstractApiController
{
    /**
     * Get Citipo prices.
     */
    #[Route('/api/prices', name: 'api_prices', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns Citipo prices.',
        content: new OA\JsonContent(ref: '#/components/schemas/Prices'),
    )]
    public function entrypoint(PricesTransformer $transformer)
    {
        return new JsonResponse($transformer->transform());
    }
}
