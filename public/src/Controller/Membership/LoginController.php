<?php

namespace App\Controller\Membership;

use App\Bridge\Turnstile\Turnstile;
use App\Client\CitipoInterface;
use App\Controller\AbstractController;
use App\Form\Member\LoginType;
use App\Form\Member\Model\LoginData;
use App\Security\CookieManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/members')]
class LoginController extends AbstractController
{
    private CookieManager $cookieManager;
    private CitipoInterface $citipo;

    public function __construct(CookieManager $cookieManager, CitipoInterface $citipo)
    {
        $this->cookieManager = $cookieManager;
        $this->citipo = $citipo;
    }

    #[Route('/login', name: 'membership_login')]
    public function login(Turnstile $turnstile, Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        $challenge = $turnstile->createCaptchaChallenge($this->getProject());

        $data = new LoginData();

        $form = $this->createForm(LoginType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($challenge && !$challenge->isValidResponse($request->request->get('cf-turnstile-response'))) {
                return $this->redirectToRoute('membership_login');
            }

            if (!$authToken = $this->citipo->login($this->getApiToken(), $data->email, $data->password)) {
                return $this->redirectToRoute('membership_login', ['error' => 'credentials']);
            }

            $response = $this->redirectToRoute('membership_area_dashboard');
            $response->headers->setCookie($this->cookieManager->createAuthCookie($authToken));

            return $response;
        }

        return $this->render('member/login.html.twig', [
            'form' => $form->createView(),
            'register_success' => $request->query->getBoolean('register'),
            'reset_success' => $request->query->getBoolean('reset'),
            'update_email_success' => $request->query->getBoolean('update-email'),
            'error' => $request->query->get('error'),
            'captcha_challenge' => $challenge,
        ]);
    }

    #[Route('/logout', name: 'membership_logout')]
    public function logout(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        $response = $this->redirectToRoute('homepage');
        $response->headers->setCookie($this->cookieManager->createLogoutCookie());

        return $response;
    }
}
