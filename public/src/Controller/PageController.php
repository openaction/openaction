<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use App\Form\Model\SubscribeNewsletterData;
use App\Form\SubscribeNewsletterType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pages')]
class PageController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    #[Route('/{id}/{slug}', name: 'page_view')]
    public function view(string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('website_pages');

        $page = $this->citipo->getPage($this->getApiToken(), $id);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        if ($page->slug !== $slug) {
            return $this->redirectToRoute('page_view', ['id' => $id, 'slug' => $page->slug], Response::HTTP_MOVED_PERMANENTLY);
        }

        $form = $this->createForm(SubscribeNewsletterType::class, new SubscribeNewsletterData(), [
            'enable_gdpr_fields' => $this->getProject()->enableGdprFields,
        ]);

        return $this->render('pages/view.html.twig', [
            'page' => $page,
            'newsletter_form' => $form->createView(),
        ]);
    }
}
