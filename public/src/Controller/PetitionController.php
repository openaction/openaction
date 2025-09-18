<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Public petitions pages.
 */
class PetitionController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("/pe/{slug}/{locale}", name="petition_view")
     */
    public function view(string $slug, string $locale)
    {
        $this->denyUnlessToolEnabled('website_petitions');

        $petition = $this->citipo->getPetition($this->getApiToken(), $slug);
        if (!$petition) {
            throw $this->createNotFoundException();
        }

        // Expect localized content keyed by locale, e.g. $petition->localized['fr']
        $localized = null;
        if (isset($petition->localized) && is_array($petition->localized) && isset($petition->localized[$locale])) {
            $localized = $petition->localized[$locale];
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
