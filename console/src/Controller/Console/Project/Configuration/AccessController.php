<?php

namespace App\Controller\Console\Project\Configuration;

use App\Controller\AbstractController;
use App\Form\Appearance\Model\WebsiteAccessData;
use App\Form\Appearance\WebsiteAccessType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/access')]
class AccessController extends AbstractController
{
    #[Route('', name: 'console_configuration_access')]
    public function access(EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $data = WebsiteAccessData::createFromProject($project);

        $form = $this->createForm(WebsiteAccessType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applyWebsiteAccessUpdate($data);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_configuration_access', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/access.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
