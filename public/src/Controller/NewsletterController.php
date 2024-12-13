<?php

namespace App\Controller;

use App\Bridge\Turnstile\Turnstile;
use App\Client\CitipoInterface;
use App\Form\Model\SubscribeNewsletterData;
use App\Form\SubscribeNewsletterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    /**
     * @Route("/newsletter", name="contact_newsletter")
     */
    public function subscribe(Turnstile $turnstile, CitipoInterface $citipo, Request $request)
    {
        $this->denyUnlessToolEnabled('website_newsletter');

        $challenge = $turnstile->createCaptchaChallenge($this->getProject());

        $data = new SubscribeNewsletterData();

        $form = $this->createForm(SubscribeNewsletterType::class, $data, [
            'enable_gdpr_fields' => $this->getProject()->enableGdprFields,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($challenge && !$challenge->isValidResponse($request->request->get('cf-turnstile-response'))) {
                return $this->redirectToRoute('contact_newsletter');
            }

            $payload = [
                'email' => $data->email,
                'settingsReceiveNewsletters' => true,
                'metadataSource' => 'api:'.$this->getProject()->id,
            ];

            $payload = array_merge($payload, array_filter([
                'addressCountry' => $data->country,
                'addressZipCode' => $data->zipCode,
                'profileFirstName' => $data->firstName,
                'profileLastName' => $data->lastName,
                'contactPhone' => $data->phone,
            ]));

            $citipo->persistContact($this->getApiToken(), $payload);

            return $this->redirectToRoute('contact_newsletter', ['s' => '1']);
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView(),
            'captcha_challenge' => $challenge,
            'success' => $request->query->getBoolean('s'),
            'unsubscribed' => $request->query->getBoolean('unsubscribe'),
        ]);
    }
}
