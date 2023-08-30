<?php

namespace App\Controller\Console\Api;

use App\Controller\AbstractController;
use App\Security\Csrf\GlobalCsrfTokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/api/csrf-token')]
class CsrfTokenController extends AbstractController
{
    private GlobalCsrfTokenManager $manager;

    public function __construct(GlobalCsrfTokenManager $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/refresh', name: 'console_api_csrf_token_refresh', methods: ['GET'])]
    public function refreshCsrfToken()
    {
        return new JsonResponse([
            'token' => $this->manager->getToken()->getValue(),
        ]);
    }
}
