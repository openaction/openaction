<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trombinoscope")
 */
class TrombinoscopeController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("", name="trombinoscope_list")
     */
    public function list()
    {
        $this->denyUnlessToolEnabled('website_trombinoscope');

        $trombinoscope = $this->citipo->getTrombinoscope($this->getApiToken());

        return $this->render('trombinoscope/list.html.twig', [
            'trombinoscope' => $trombinoscope,
        ]);
    }

    /**
     * @Route("/{id}/{slug}", name="trombinoscope_view")
     */
    public function view(string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('website_trombinoscope');

        $person = $this->citipo->getTrombinoscopePerson($this->getApiToken(), $id);

        if (!$person) {
            throw $this->createNotFoundException();
        }

        if ($person->slug !== $slug) {
            return $this->redirectToRoute('trombinoscope_view', ['id' => $id, 'slug' => $person->slug], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('trombinoscope/view.html.twig', [
            'person' => $person,
        ]);
    }
}
