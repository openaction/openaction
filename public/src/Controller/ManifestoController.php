<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manifesto')]
class ManifestoController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    #[Route('', name: 'manifesto_list')]
    public function list(Request $request)
    {
        $this->denyUnlessToolEnabled('website_manifesto');

        $manifesto = $this->citipo->getManifesto($this->getApiToken());

        return $this->render('manifesto/list.html.twig', [
            'manifesto' => $manifesto,
            'statusFilter' => $request->query->get('status'),
        ]);
    }

    #[Route('/{id}/{slug}', name: 'manifesto_view')]
    public function view(string $id, string $slug, Request $request)
    {
        $this->denyUnlessToolEnabled('website_manifesto');

        $topic = $this->citipo->getManifestoTopic($this->getApiToken(), $id);

        if (!$topic) {
            throw $this->createNotFoundException();
        }

        if ($topic->slug !== $slug) {
            return $this->redirectToRoute('manifesto_view', ['id' => $id, 'slug' => $topic->slug], Response::HTTP_MOVED_PERMANENTLY);
        }

        $manifesto = $this->citipo->getManifesto($this->getApiToken());

        return $this->render('manifesto/view.html.twig', [
            'manifesto' => $manifesto,
            'topic' => $topic,
            'statusFilter' => $request->query->get('status'),
        ]);
    }
}
