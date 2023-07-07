<?php

namespace App\Controller;

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
    public function subscribe(CitipoInterface $citipo, Request $request)
    {
        $this->denyUnlessToolEnabled('website_newsletter');

        $data = new SubscribeNewsletterData();

        $form = $this->createForm(SubscribeNewsletterType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payload = [
                'email' => $data->email,
                'addressCountry' => $data->country,
                'addressZipCode' => $data->zipCode,
                'settingsReceiveNewsletters' => true,
                'metadataSource' => 'api:'.$this->getProject()->id,
            ];

            if ($data->firstName) {
                $payload['profileFirstName'] = $data->firstName;
            }

            if ($data->lastName) {
                $payload['profileLastName'] = $data->lastName;
            }

            if ($data->phone) {
                $payload['contactPhone'] = $data->phone;
            }

            $citipo->persistContact($this->getApiToken(), $payload);

            return $this->redirectToRoute('contact_newsletter', ['s' => '1']);
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView(),
            'success' => $request->query->getBoolean('s'),
        ]);
    }
}
