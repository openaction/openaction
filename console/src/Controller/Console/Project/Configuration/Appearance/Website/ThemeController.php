<?php

namespace App\Controller\Console\Project\Configuration\Appearance\Website;

use App\Controller\AbstractController;
use App\Form\Appearance\Model\WebsiteThemeData;
use App\Form\Appearance\WebsiteThemeType;
use App\Platform\Permissions;
use App\Repository\Theme\WebsiteThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance/website/theme')]
class ThemeController extends AbstractController
{
    #[Route('', name: 'console_configuration_appearance_website_theme')]
    public function theme(WebsiteThemeRepository $themeRepository, EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();

        $themesQb = $themeRepository->createChoiceQueryBuilder($project->getOrganization());

        $data = WebsiteThemeData::createFromProject($project);

        $form = $this->createForm(WebsiteThemeType::class, $data, ['themes_query_builder' => clone $themesQb]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applyWebsiteThemeUpdate($data);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_appearance_website_theme', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/appearance/website/theme.html.twig', [
            'form' => $form->createView(),
            'themes' => $themesQb->getQuery()->getResult(),
        ]);
    }
}
