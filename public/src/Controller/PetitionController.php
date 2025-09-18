<?php

namespace App\Controller;

use App\Bridge\Turnstile\Turnstile;
use App\Client\CitipoInterface;
use App\FormBuilder\SymfonyFormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PetitionController extends AbstractController
{
    public function __construct(
        private readonly Turnstile $turnstile,
        private readonly SymfonyFormBuilder $formBuilder,
        private readonly CitipoInterface $citipo,
    ) {
    }

    #[Route('/petition/{slug}', name: 'petition_view')]
    public function view(Request $request, string $slug)
    {
        $this->denyUnlessToolEnabled('website_petitions');

        $petition = $this->citipo->getPetition($this->getApiToken(), $slug);
        if (!$petition) {
            throw $this->createNotFoundException();
        }

        $locale = $request->query->get('locale');
        $localized = null;
        foreach ($petition->localizations as $l) {
            if ($l->locale === $locale) {
                $localized = $l;
            }
        }

        if (!$localized) {
            throw $this->createNotFoundException();
        }

        $challenge = $this->turnstile->createCaptchaChallenge($this->getProject());

        $form = $this->formBuilder->createFromBlocks($localized->form->blocks->data, [], $this->getProject()->enableGdprFields);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($challenge && !$challenge->isValidResponse($request->request->get('cf-turnstile-response'))) {
                return $this->redirectToRoute('petition_view', ['slug' => $slug, 'locale' => $locale]);
            }

            // Persist answer
            $answers = $this->formBuilder->normalizeFormData($localized->form->blocks->data, $form->getData());
            $this->citipo->createFormAnswer($this->getApiToken(), $localized->form->id, $answers);

            // Persist picture if requested
            $email = $this->formBuilder->getEmailValue($localized->form->blocks->data, $form->getData());
            $picture = $this->formBuilder->getPictureValue($localized->form->blocks->data, $form->getData());

            if ($email && $picture) {
                $this->citipo->persistContactPicture(
                    $this->getApiToken(),
                    $this->citipo->getContactStatus($this->getApiToken(), $email)->id,
                    $picture
                );
            }

            return $this->redirectToRoute('petition_view', ['slug' => $slug, 'locale' => $locale, 's' => '1']);
        }

        return $this->render('petitions/view.html.twig', [
            'petition' => $petition,
            'localized' => $localized,
        ]);
    }
}
