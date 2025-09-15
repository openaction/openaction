<?php

namespace App\Controller\Api\Payments;

use App\Bridge\Mollie\MollieConnect;
use App\Controller\Api\AbstractApiController;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Payments')]
#[Route('/api/payments/mollie')]
class MollieController extends AbstractApiController
{
    public function __construct(private readonly MollieConnect $mollieConnect)
    {
    }

    /**
     * Generate or return a valid Mollie access token for the current project organization.
     */
    #[Route('/token', name: 'api_payments_mollie_token', methods: ['POST'])]
    public function token(EntityManagerInterface $em): Response
    {
        /** @var Project $project */
        $project = $this->getUser();
        $orga = $project->getOrganization();

        $accessToken = $orga->getMollieConnectAccessToken();
        $refreshToken = $orga->getMollieConnectRefreshToken();
        if (!$accessToken || !$refreshToken) {
            return new JsonResponse(['error' => 'Mollie Connect is not configured for this organization.'], Response::HTTP_BAD_REQUEST);
        }

        $expiresAt = $orga->getMollieConnectAccessTokenExpiresAt();
        $nowPlus5 = (new \DateTimeImmutable('now'))->modify('+5 minutes');

        if ($expiresAt instanceof \DateTimeInterface && $expiresAt > $nowPlus5) {
            return new JsonResponse([
                '_resource' => 'MollieAccessToken',
                'accessToken' => $accessToken,
                'expiresAt' => $expiresAt->format(DATE_ATOM),
            ]);
        }

        // Refresh the access token using the refresh token
        $tokens = $this->mollieConnect->refreshAccessToken($refreshToken);

        $orga->setMollieConnectAccessToken($tokens['access_token'] ?? null);
        $orga->setMollieConnectRefreshToken($tokens['refresh_token'] ?? null);
        $orga->setMollieConnectAccessTokenExpiresAt(
            isset($tokens['expires_in']) && is_numeric($tokens['expires_in'])
                ? (new \DateTimeImmutable('now'))->modify('+'.((int) $tokens['expires_in']).' seconds')
                : null
        );
        $em->persist($orga);
        $em->flush();

        return new JsonResponse([
            '_resource' => 'MollieAccessToken',
            'accessToken' => $orga->getMollieConnectAccessToken(),
            'expiresAt' => $orga->getMollieConnectAccessTokenExpiresAt()?->format(DATE_ATOM),
        ]);
    }
}

