<?php

namespace App\Controller\Api;

use App\Api\Transformer\AreaTransformer;
use App\Repository\AreaRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'General')]
#[Route('/api/areas')]
class AreaController extends AbstractApiController
{
    private AreaRepository $repo;

    public function __construct(AreaRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get the list of countries supported by Citipo.
     *
     * This endpoint is NOT paginated.
     */
    #[Route('/countries', name: 'api_areas_countries', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of countries supported by Citipo.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Area')),
    )]
    public function countries(AreaTransformer $transformer)
    {
        return $this->handleApiCollection($this->repo->findAllCountries(), $transformer, false);
    }

    /**
     * Check whether the given ZIP code is valid for the given country.
     */
    #[Route('/validate/{countryCode}/{zipCode}', name: 'api_areas_validate', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the validation status.',
        content: new OA\JsonContent(
            title: 'AreaValidation',
            properties: [
                new OA\Property(property: '_resource', type: 'string'),
                new OA\Property(property: 'status', type: 'string', enum: ['country_not_found', 'zip_code_not_found', 'ok']),
            ],
        ),
    )]
    public function validate(string $countryCode, string $zipCode)
    {
        if (!$country = $this->repo->searchCountry($countryCode)) {
            return new JsonResponse(['_resource' => 'AreaValidation', 'status' => 'country_not_found']);
        }

        if (!$this->repo->searchZipCode($country, trim($zipCode))) {
            return new JsonResponse(['_resource' => 'AreaValidation', 'status' => 'zip_code_not_found']);
        }

        return new JsonResponse(['_resource' => 'AreaValidation', 'status' => 'ok']);
    }
}
