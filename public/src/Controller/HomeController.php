<?php

namespace App\Controller;

use App\Bridge\Turnstile\Turnstile;
use App\Form\Model\SubscribeNewsletterData;
use App\Form\SubscribeNewsletterType;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     */
    public function index(Turnstile $turnstile)
    {
        $challenge = $turnstile->createCaptchaChallenge($this->getProject());

        return $this->render('home/index.html.twig', [
            'captcha_challenge' => $challenge,
            'newsletter_form' => $this->createForm(SubscribeNewsletterType::class, new SubscribeNewsletterData())->createView(),
        ]);
    }
}
