<?php

namespace App\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\Entity\Website\PageBlock;
use App\Platform\Permissions;
use App\Website\PageBlockManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance/website/homepage')]
class ConfigureController extends AbstractController
{
    use ContentEditorUploadControllerTrait;

    #[Route('/block/{id}/configure', name: 'console_configuration_appearance_website_homepage_block_configure')]
    public function configure(EntityManagerInterface $manager, PageBlockManager $blockManager, Request $request, PageBlock $block)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($block);

        if (!$formType = $blockManager->getConfigForm($block)) {
            return $this->redirectToRoute('console_configuration_appearance_website_homepage', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        $form = $this->createForm($formType, $block->getConfig(), ['project' => $this->getProject()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $block->setConfig($form->getData());

            $manager->persist($block);
            $manager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['status' => 'ok']);
            }

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_appearance_website_homepage_block_configure', [
                'projectUuid' => $this->getProject()->getUuid(),
                'id' => $block->getId(),
            ]);
        }

        return $this->render('console/project/configuration/appearance/website/homepage/configure_'.$block->getType().'.html.twig', [
            'form' => $form->createView(),
            'block' => $block,
            'current_project' => $this->getProject(),
        ]);
    }

    #[Route('/upload', name: 'console_configuration_appearance_website_homepage_block_upload', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($this->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }
}
