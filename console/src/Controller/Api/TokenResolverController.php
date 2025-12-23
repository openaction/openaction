<?php

namespace App\Controller\Api;

use App\Repository\ProjectRepository;
use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenResolverController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projects,
        #[Autowire('%env(APP_TOKEN_RESOLVER_KEY)%')]
        private readonly string $tokenResolverKey,
    ) {
    }

    #[Route('/api/token-resolver/{hostname}', name: 'api_token_resolver', methods: ['GET'], requirements: ['hostname' => '.+'])]
    #[Ignore]
    public function __invoke(Request $request, string $hostname): JsonResponse
    {
        $key = (string) $request->query->get('key', '');

        if (!hash_equals($this->tokenResolverKey, $key)) {
            return new JsonResponse(['error' => 'Invalid token resolver key.'], Response::HTTP_FORBIDDEN);
        }

        $token = $this->projects->findDomainsTokens()[strtolower($hostname)] ?? null;
        if (!$token) {
            throw $this->createNotFoundException('Hostname not found.');
        }

        return new JsonResponse(['token' => $token]);
    }
}
