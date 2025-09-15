<?php

namespace App\Controller\Bridge;

use App\Bridge\Mollie\MollieConnectInterface;
use App\Controller\AbstractController;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bridge/mollie/connect')]
class MollieConnectController extends AbstractController
{
    public function __construct(private readonly MollieConnectInterface $mollieConnect)
    {
    }

    #[Route('/callback', name: 'bridge_mollie_connect_callback')]
    public function callback(Request $request, OrganizationRepository $organizations, EntityManagerInterface $em)
    {
        $error = $request->query->get('error');
        if ($error) {
            $this->addFlash('error', (string) $request->query->get('error_description', $error));

            return $this->redirectToRoute('console_organization_integrations', [
                'organizationUuid' => $this->getOrganization()?->getUuid() ?: 'cbeb774c-284c-43e3-923a-5a2388340f91',
            ]);
        }

        $stateRaw = (string) $request->query->get('state');
        $code = (string) $request->query->get('code');

        if (!$stateRaw || !$code) {
            throw $this->createAccessDeniedException('Missing state or code.');
        }

        $state = json_decode(base64_decode(strtr($stateRaw, '-_', '+/')), true);
        if (!\is_array($state) || empty($state['o']) || empty($state['s'])) {
            throw $this->createAccessDeniedException('Invalid state.');
        }

        // Find organization by UUID from state
        if (!$orga = $organizations->findOneBy(['uuid' => $state['o']])) {
            throw $this->createNotFoundException('Organization not found');
        }

        // Verify CSRF/state stored at connect-time
        $expected = (string) $request->getSession()->get('mollie_oauth_state_'.$orga->getId());
        if (!$expected || !hash_equals($expected, (string) $state['s'])) {
            throw $this->createAccessDeniedException('Invalid OAuth state.');
        }

        // Exchange code for tokens with the same fixed redirect_uri
        $tokens = $this->mollieConnect->exchangeCodeForTokens($code);

        $orga->setMollieConnectAccessToken($tokens['access_token'] ?? null);
        $orga->setMollieConnectRefreshToken($tokens['refresh_token'] ?? null);
        $em->persist($orga);
        $em->flush();

        $this->addFlash('success', 'integrations.updated_success');

        return $this->redirectToRoute('console_organization_integrations', [
            'organizationUuid' => $orga->getUuid()->toRfc4122(),
        ]);
    }
}
