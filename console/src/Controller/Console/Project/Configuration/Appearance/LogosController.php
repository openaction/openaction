<?php

namespace App\Controller\Console\Project\Configuration\Appearance;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Form\Appearance\LogosType;
use App\Form\Appearance\Model\LogosData;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance')]
class LogosController extends AbstractController
{
    #[Route('/logos', name: 'console_configuration_appearance_logos')]
    public function logos(CdnUploader $uploader, EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();

        $data = new LogosData();

        $form = $this->createForm(LogosType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload the files
            $toRemove = [];

            if ($data->appearanceLogoWhite) {
                $toRemove[] = $project->getAppearanceLogoWhite();
                $data->appearanceLogoWhiteUpload = $uploader->upload(
                    CdnUploadRequest::createProjectLogoRequest($project, $data->appearanceLogoWhite)
                );
            }

            if ($data->appearanceLogoDark) {
                $toRemove[] = $project->getAppearanceLogoDark();
                $data->appearanceLogoDarkUpload = $uploader->upload(
                    CdnUploadRequest::createProjectLogoRequest($project, $data->appearanceLogoDark)
                );
            }

            if ($data->appearanceIcon) {
                $toRemove[] = $project->getAppearanceIcon();
                $data->appearanceIconUpload = $uploader->upload(
                    CdnUploadRequest::createProjectIconRequest($project, $data->appearanceIcon)
                );
            }

            $project->applyLogosUpdate($data);

            $manager->persist($project);
            $manager->flush();

            // Remove old images
            foreach (array_filter($toRemove) as $upload) {
                $manager->remove($upload);
            }

            $manager->flush();

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_appearance_logos', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/appearance/logos.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
