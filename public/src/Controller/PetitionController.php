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

    #[Route("/petition/{slug}", name: "petition_view")]
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

        dd($localized);

        return $this->render('petitions/view.html.twig', [
            'petition' => $petition,
            'localized' => $localized,
        ]);
    }
}
