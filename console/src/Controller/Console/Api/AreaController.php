<?php

namespace App\Controller\Console\Api;

use App\Controller\AbstractController;
use App\Repository\AreaRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/api/areas')]
class AreaController extends AbstractController
{
    private AreaRepository $repository;

    public function __construct(AreaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/search', name: 'console_api_areas_search', methods: ['GET'])]
    public function search(Request $request)
    {
        return new JsonResponse($this->repository->findRawChildrenOf($request->query->getInt('p')));
    }
}
