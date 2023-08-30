<?php

namespace App\Controller\Console\Partner;

use App\Controller\AbstractController;
use App\Entity\Theme\WebsiteTheme;
use App\Form\Theme\Model\WebsiteThemeData;
use App\Form\Theme\WebsiteThemeType;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Theme\Consumer\SyncThemeMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/partner/themes')]
class ThemeController extends AbstractController
{
    private WebsiteThemeRepository $websiteThemeRepo;

    public function __construct(WebsiteThemeRepository $websiteThemeRepo)
    {
        $this->websiteThemeRepo = $websiteThemeRepo;
    }

    #[Route('/manage', name: 'console_partner_themes_manage', methods: ['GET', 'POST'])]
    public function manage()
    {
        return $this->render('console/partner/theme/manage.html.twig', [
            'websiteThemes' => $this->websiteThemeRepo->findByAuthor($this->getUser()),
        ]);
    }

    #[Route('/{uuid}/configure', name: 'console_partner_themes_configure', methods: ['GET', 'POST'])]
    public function configure(WebsiteTheme $theme, Request $request)
    {
        if ($this->getUser()->getId() !== $theme->getAuthor()->getId()) {
            throw $this->createNotFoundException();
        }

        $data = new WebsiteThemeData($theme->getForOrganizations());

        $form = $this->createForm(WebsiteThemeType::class, $data, [
            'accessible_organizations' => $this->getUser()->getOrganizations(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->websiteThemeRepo->updateOrganizations($theme, $data->forOrganizations);

            return $this->redirectToRoute('console_partner_themes_manage');
        }

        return $this->render('console/partner/theme/configure.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/sync', name: 'console_partner_themes_sync', methods: ['GET'])]
    public function sync(EntityManagerInterface $em, MessageBusInterface $bus, WebsiteTheme $theme, Request $request)
    {
        if ($this->getUser()->getId() !== $theme->getAuthor()->getId()) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessValidCsrf($request);

        $theme->setIsUpdating(true);
        $theme->setUpdateError('');

        $em->persist($theme);
        $em->flush();

        $bus->dispatch(new SyncThemeMessage($theme->getId()));

        return $this->redirectToRoute('console_partner_themes_manage');
    }

    #[Route('/link', name: 'console_partner_themes_link', methods: ['GET'])]
    public function link(Request $request)
    {
        // Apply the change after a timeout to avoid race conditions
        if ($request->query->getBoolean('apply')) {
            if ($installationId = $request->query->get('installation_id')) {
                $this->websiteThemeRepo->linkAuthor($installationId, $this->getUser());
            }

            return $this->redirectToRoute('console_partner_themes_manage');
        }

        return $this->render('console/partner/theme/link.html.twig', [
            'installationId' => $request->query->get('installation_id'),
        ]);
    }
}
