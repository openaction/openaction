<?php

namespace App\Controller\Console\Project\Configuration;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\Form\Project\Model\UpdateMembershipMainPageData;
use App\Form\Project\UpdateMembershipMainPageType;
use App\Form\Project\UpdateMembershipSettingsType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/settings/membership')]
class MembershipController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    #[Route('', name: 'console_configuration_membership')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/settings/membership/index.html.twig');
    }

    #[Route('/homepage', name: 'console_configuration_membership_homepage')]
    public function homepage()
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/settings/membership/homepage.html.twig', [
            'pageContent' => $this->getProject()->getMembershipMainPage(),
            'form' => $this->createForm(UpdateMembershipMainPageType::class)->createView(),
        ]);
    }

    #[Route('/homepage/update', name: 'console_configuration_membership_homepage_update', methods: ['POST'])]
    public function homepageUpdate(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $data = new UpdateMembershipMainPageData();

        $form = $this->createForm(UpdateMembershipMainPageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $project = $this->getProject();
        $project->setMembershipMainPage($data->content);

        $em->persist($project);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/homepage/upload', name: 'console_configuration_membership_homepage_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($this->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/form', name: 'console_configuration_membership_form')]
    public function form(EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();
        $settings = $project->getMembershipFormSettings();
        $form = $this->createForm(UpdateMembershipSettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setMembershipFormSettings($settings);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.membership_success');

            return $this->redirectToRoute('console_configuration_membership', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/settings/membership/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
