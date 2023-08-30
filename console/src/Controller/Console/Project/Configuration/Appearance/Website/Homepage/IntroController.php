<?php

namespace App\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Form\Appearance\Model\WebsiteIntroData;
use App\Form\Appearance\WebsiteIntroType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance/website/intro')]
class IntroController extends AbstractController
{
    #[Route('', name: 'console_configuration_appearance_website_intro')]
    public function intro(CdnUploader $uploader, EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();

        $data = WebsiteIntroData::createFromProject($project);

        $form = $this->createForm(WebsiteIntroType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applyWebsiteIntroUpdate($data);
            $manager->persist($project);

            if ($data->websiteMainImage) {
                $toRemove = $project->getWebsiteMainImage();

                $project->setWebsiteMainImage($uploader->upload(
                    CdnUploadRequest::createWebsiteHomeMainImageRequest($project, $data->websiteMainImage)
                ));

                $manager->persist($project);

                if ($toRemove) {
                    $manager->remove($toRemove);
                }
            }

            if ($data->websiteMainVideo) {
                $toRemove = $project->getWebsiteMainVideo();

                $project->setWebsiteMainVideo($uploader->upload(
                    CdnUploadRequest::createWebsiteHomeMainVideoRequest($project, $data->websiteMainVideo)
                ));

                $manager->persist($project);

                if ($toRemove) {
                    $manager->remove($toRemove);
                }
            }

            $manager->flush();

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_appearance_website_intro', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/appearance/website/homepage/intro.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/remove-image', name: 'console_configuration_appearance_website_intro_remove_image')]
    public function removeImage(EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if ($toRemove = $project->getWebsiteMainImage()) {
            $project->setWebsiteMainImage(null);

            $manager->persist($project);
            $manager->flush();

            $manager->remove($toRemove);
            $manager->flush();
        }

        $this->addFlash('success', 'configuration.appearance_success');

        return $this->redirectToRoute('console_configuration_appearance_website_intro', [
            'projectUuid' => $project->getUuid(),
        ]);
    }

    #[Route('/remove-video', name: 'console_configuration_appearance_website_intro_remove_video')]
    public function removeVideo(EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $project);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if ($toRemove = $project->getWebsiteMainVideo()) {
            $project->setWebsiteMainVideo(null);

            $manager->persist($project);
            $manager->flush();

            $manager->remove($toRemove);
            $manager->flush();
        }

        $this->addFlash('success', 'configuration.appearance_success');

        return $this->redirectToRoute('console_configuration_appearance_website_intro', [
            'projectUuid' => $project->getUuid(),
        ]);
    }
}
