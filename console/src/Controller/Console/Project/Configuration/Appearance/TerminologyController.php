<?php

namespace App\Controller\Console\Project\Configuration\Appearance;

use App\Controller\AbstractController;
use App\Form\Appearance\TerminologyType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance')]
class TerminologyController extends AbstractController
{
    #[Route('/terminology', name: 'console_configuration_appearance_terminology')]
    public function terminology(EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();

        $terminology = $project->getAppearanceTerminology();

        $form = $this->createForm(TerminologyType::class, $terminology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setAppearanceTerminology($terminology);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_appearance_terminology', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/appearance/terminology.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
