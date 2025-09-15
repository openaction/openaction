<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Bridge\Mollie\MollieConnect;
use App\Controller\AbstractController;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/console/organization/{organizationUuid}/integrations/mollie')]
class MollieController extends AbstractController
{
    public function __construct(private readonly MollieConnect $mollieConnect)
    {
    }

    #[Route('/connect', name: 'console_organization_integrations_mollie_connect')]
    public function connect(Request $request, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $this->denyIfSubscriptionExpired();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->requireTwoFactorAuthIfForced();

        $orga = $this->getOrganization();

        $state = bin2hex(random_bytes(16));
        $request->getSession()->set('mollie_oauth_state_'.$orga->getId(), $state);

        $redirectUri = $urlGenerator->generate('console_organization_integrations_mollie_callback', [
            'organizationUuid' => $orga->getUuid()->toRfc4122(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $authorizationUrl = $this->mollieConnect->getAuthorizationUrl($redirectUri, $state, [
            'organizations.read', 'profiles.read', 'payments.read', 'payments.write',
        ]);

        return new RedirectResponse($authorizationUrl);
    }

    #[Route('/callback', name: 'console_organization_integrations_mollie_callback')]
    public function callback(EntityManagerInterface $em, Request $request)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->requireTwoFactorAuthIfForced();

        $orga = $this->getOrganization();

        $error = $request->query->get('error');
        if ($error) {
            $this->addFlash('error', (string) $request->query->get('error_description', $error));

            return $this->redirectToRoute('console_organization_integrations', [
                'organizationUuid' => $orga->getUuid()->toRfc4122(),
            ]);
        }

        $state = (string) $request->query->get('state');
        $code = (string) $request->query->get('code');
        $expectedState = (string) $request->getSession()->get('mollie_oauth_state_'.$orga->getId());

        if (!$state || !$code || !hash_equals($expectedState, $state)) {
            throw $this->createAccessDeniedException('Invalid OAuth state.');
        }

        $redirectUri = $this->generateUrl('console_organization_integrations_mollie_callback', [
            'organizationUuid' => $orga->getUuid()->toRfc4122(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $tokens = $this->mollieConnect->exchangeCodeForTokens($code, $redirectUri);

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

