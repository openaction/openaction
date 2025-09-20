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
            'nextGoal' => $this->computeNextGoal($petition->signatures_count, $petition->signatures_goal),
            'formData' => $localized->form,
            'form' => $form->createView(),
            'captcha_challenge' => $challenge,
            'success' => $request->query->getBoolean('s'),
        ]);
    }

    private function computeNextGoal(int $signaturesCount, int $signaturesGoal): int
    {
        if (!$signaturesGoal) {
            $signaturesGoal = PHP_INT_MAX;
        }

        // Cas de départ : quasi zéro signature
        if ($signaturesCount <= 2) {
            return min(10, $signaturesGoal);
        }

        // Si proche du but (moins de 10% restant), viser directement le but
        if ($signaturesCount >= 0.9 * $signaturesGoal) {
            return $signaturesGoal;
        }

        // Calcul de la limite max = min(double des signatures, but final)
        $max = min($signaturesGoal, 2 * $signaturesCount);

        // Choix du pas d'arrondi en fonction de l'ordre de grandeur
        if ($signaturesCount < 100) {
            $step = 10;
        } elseif ($signaturesCount < 1000) {
            $step = 100;
        } elseif ($signaturesCount < 100000) {
            $step = 1000;
        } else {
            $step = 5000; // ou 10000 si tu veux encore plus rond
        }

        // Arrondi au multiple supérieur
        $next = (int) (ceil($max / $step) * $step);

        // Ne pas dépasser l'objectif final
        return min($next, $signaturesGoal);
    }
}
