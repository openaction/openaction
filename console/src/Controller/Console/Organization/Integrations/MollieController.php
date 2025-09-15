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

        // Encode organization in state along with CSRF token
        $statePayloadRaw = json_encode([
            'o' => $orga->getUuid()->toRfc4122(),
            's' => $state,
        ]);
        $statePayload = rtrim(strtr(base64_encode($statePayloadRaw), '+/', '-_'), '=');

        $authorizationUrl = $this->mollieConnect->getAuthorizationUrl($statePayload, [
            'organizations.read', 'profiles.read', 'payments.read', 'payments.write',
        ]);

        return new RedirectResponse($authorizationUrl);
    }

    // Callback moved to Bridge\MollieConnectController
}
