<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PetitionController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
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

        return $this->render('petitions/view.html.twig', [
            'petition' => $petition,
            'localized' => $localized,
        ]);
    }
}
