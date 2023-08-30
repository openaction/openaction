<?php

namespace App\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Controller\AbstractController;
use App\Form\Appearance\PageBlock\CreateHomeBlockType;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Website\PageBlock\BlockInterface;
use App\Website\PageBlock\HomeSocialsBlock;
use App\Website\PageBlockManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance/website/homepage')]
class CreateController extends AbstractController
{
    #[Route('/block/create', name: 'console_configuration_appearance_website_homepage_block_create')]
    public function create(EntityManagerInterface $manager, PageBlockManager $blockManager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();

        $types = $blockManager->getTypes(BlockInterface::PAGE_HOME);
        if (!$project->isFeatureInPlan(Features::FEATURE_WEBSITE_SOCIAL_IFRAMES)) {
            unset($types[array_search(HomeSocialsBlock::TYPE, $types, true)]);
        }

        $form = $this->createForm(CreateHomeBlockType::class, [], ['types' => $types]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $block = $blockManager->createBlock($project, BlockInterface::PAGE_HOME, $form->getData()['type']);

            $manager->persist($block);
            $manager->flush();

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_appearance_website_homepage_block_configure', [
                'projectUuid' => $project->getUuid(),
                'id' => $block->getId(),
            ]);
        }

        return $this->render('console/project/configuration/appearance/website/homepage/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
