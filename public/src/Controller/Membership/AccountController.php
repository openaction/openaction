<?php

namespace App\Controller\Membership;

use App\Client\CitipoInterface;
use App\Form\Member\Model\UpdateAccountData;
use App\Form\Member\Model\UpdateEmailData;
use App\Form\Member\UpdateAccountType;
use App\Form\Member\UpdateEmailType;
use App\Security\CookieManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/members/area/account')]
class AccountController extends AbstractMembershipController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    #[Route('', name: 'membership_area_account')]
    public function details(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$project = $this->getProject()) {
            throw $this->createNotFoundException();
        }

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $data = UpdateAccountData::createFromContact($contact);

        $form = $this->createForm(UpdateAccountType::class, $data, ['membership_settings' => $project->membership]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->citipo->persistContact($this->getApiToken(), $data->createApiPayload());

            return $this->redirectToRoute('membership_area_dashboard', ['saved' => '1']);
        }

        return $this->render('member/area/account/details.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'saved' => $request->query->getBoolean('saved'),
        ]);
    }

    #[Route('/update-email', name: 'membership_area_update_email')]
    public function updateEmail(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->getProject()) {
            throw $this->createNotFoundException();
        }

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $data = new UpdateEmailData($contact->email);

        $form = $this->createForm(UpdateEmailType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->citipo->requestEmailUpdate($this->getApiToken(), $contact->id, $data->email);

            return $this->redirectToRoute('membership_area_update_email', ['saved' => '1']);
        }

        return $this->render('member/area/account/updateEmail.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'saved' => $request->query->getBoolean('saved'),
        ]);
    }

    #[Route('/update-email/confirm/{id}/{token}', name: 'membership_area_update_email_confirm')]
    public function updateEmailConfirm(CookieManager $cookieManager, Request $request, string $id, string $token)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->getProject() || !$this->authorize($request)) {
            throw $this->createNotFoundException();
        }

        if (!$this->citipo->confirmEmailUpdate($this->getApiToken(), $id, $token)) {
            throw $this->createNotFoundException();
        }

        // Require login
        $response = $this->redirectToRoute('membership_login', ['update-email' => '1']);
        $response->headers->setCookie($cookieManager->createLogoutCookie());

        return $response;
    }

    #[Route('/unregister', name: 'membership_area_unregister')]
    public function unregister(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->getProject()) {
            throw $this->createNotFoundException();
        }

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $form = $this->createFormBuilder()->getForm(); // Empty form to protect against CSRF
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->citipo->requestUnregister($this->getApiToken(), $contact->id);

            return $this->redirectToRoute('membership_area_unregister', ['saved' => '1']);
        }

        return $this->render('member/area/account/unregister.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'saved' => $request->query->getBoolean('saved'),
        ]);
    }

    #[Route('/unregister/confirm/{id}/{token}', name: 'membership_area_unregister_confirm')]
    public function unregisterConfirm(CookieManager $cookieManager, Request $request, string $id, string $token)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->getProject() || !$this->authorize($request)) {
            throw $this->createNotFoundException();
        }

        if (!$this->citipo->confirmUnregister($this->getApiToken(), $id, $token)) {
            throw $this->createNotFoundException();
        }

        // Logout
        $response = $this->redirectToRoute('homepage');
        $response->headers->setCookie($cookieManager->createLogoutCookie());

        return $response;
    }
}
