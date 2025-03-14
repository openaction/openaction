<?php

namespace App\Controller\Console\Organization\Community;

use App\Bridge\Unlayer\UnlayerInterface;
use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\SendgridMailFactory;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\EmailAutomation;
use App\Form\Community\EmailAutomationMetaDataType;
use App\Form\Community\Model\EmailAutomationMetaData;
use App\Platform\Permissions;
use App\Repository\Community\EmailAutomationRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/organization/{organizationUuid}/community/automations')]
class EmailAutomationController extends AbstractController
{
    use ApiControllerTrait;

    private EmailAutomationRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(EmailAutomationRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('', defaults: ['enabled' => true], name: 'console_organization_community_automation')]
    #[Route('/disabled', defaults: ['enabled' => false], name: 'console_organization_community_automation_disabled')]
    public function index(bool $enabled)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/organization/community/automation/'.($enabled ? 'index' : 'disabled').'.html.twig', [
            'automations' => $this->repository->findAllFor($this->getOrganization(), $enabled),
        ]);
    }

    #[Route('/create-from-template/{templateId}', defaults: ['templateId' => null], name: 'console_organization_community_automation_create_template')]
    public function createFromTemplate(UnlayerInterface $unlayer, TranslatorInterface $translator, Request $request, ?string $templateId)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $templates = $unlayer->getAutomationTemplates();

        if ($templateId) {
            $this->denyUnlessValidCsrf($request);

            if (!$template = $templates[$templateId] ?? null) {
                throw $this->createNotFoundException();
            }

            $automation = EmailAutomation::createDefault(
                $this->getOrganization(),
                $translator->trans('automation.create.name', [], 'organization_community'),
                'no-reply@'.($this->getOrganization()->getMainDomain() ?: 'example.org'),
                $translator->trans('create.fromName', [
                    '%user%' => $this->getUser()->getFirstName(),
                    '%organization%' => $this->getOrganization()->getName(),
                ], 'project_emailings'),
                $translator->trans('automation.create.subject', [], 'organization_community'),
                1 + $this->repository->count(['organization' => $this->getOrganization()])
            );

            $automation->applyUnlayerUpdate($template['design'], '');

            $this->em->persist($automation);
            $this->em->flush();

            return $this->redirectToRoute('console_organization_community_automation_content', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $automation->getUuid(),
            ]);
        }

        return $this->render('console/organization/community/automation/create_template.html.twig', [
            'templates' => $templates,
        ]);
    }

    #[Route('/{uuid}/preview', name: 'console_organization_community_automation_preview')]
    public function preview(SendgridMailFactory $messageFactory, EmailAutomation $automation)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($automation);

        return new Response($messageFactory->createAutomationBody($automation, true));
    }

    #[Route('/{uuid}/metadata', name: 'console_organization_community_automation_metadata', methods: ['GET', 'POST'])]
    public function metadata(EmailAutomation $automation, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($automation);

        $metadata = EmailAutomationMetaData::createFromAutomation($automation);

        $form = $this->createForm(EmailAutomationMetaDataType::class, $metadata, [
            'organization' => $this->getOrganization(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $automation->applyMetadata($metadata);

            $this->em->persist($automation);
            $this->em->flush();

            $this->addFlash('success', 'automations.updated_success');

            if (!$automation->getContent()) {
                return $this->redirectToRoute('console_organization_community_automation_content', [
                    'organizationUuid' => $this->getOrganization()->getUuid(),
                    'uuid' => $automation->getUuid(),
                ]);
            }

            return $this->redirectToRoute('console_organization_community_automation_metadata', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $automation->getUuid(),
            ]);
        }

        return $this->render('console/organization/community/automation/metadata.html.twig', [
            'automation' => $automation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/content', name: 'console_organization_community_automation_content', methods: ['GET'])]
    public function content(EmailAutomation $automation)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($automation);

        return $this->render('console/organization/community/automation/content.html.twig', [
            'automation' => $automation,
        ]);
    }

    #[Route('/{uuid}/content/update', name: 'console_organization_community_automation_content_update', methods: ['POST'])]
    public function updateContent(EmailAutomation $automation, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($automation);

        $automation->applyUnlayerUpdate(
            Json::decode($request->request->get('design')),
            $request->request->get('content')
        );

        $this->em->persist($automation);
        $this->em->flush();

        return new JsonResponse(['success' => true], 200);
    }

    #[Route('/{uuid}/content/upload', name: 'console_organization_community_automation_content_upload_images', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, EmailAutomation $automation, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($automation);

        if (!$uploadedFile = $request->files->get('file')) {
            throw $this->createAccessDeniedException();
        }

        if ($uploadedFile->getSize() > 26214400) { // 25 MiB
            throw new BadRequestHttpException('File too large.');
        }

        if (!str_starts_with($uploadedFile->getMimeType(), 'image/')) {
            throw new BadRequestHttpException('Invalid image.');
        }

        $upload = $uploader->upload(CdnUploadRequest::createOrganizationEmailAutomationRequest($uploadedFile));

        return new JsonResponse(['url' => $router->generateUrl($upload)]);
    }

    #[Route('/{uuid}/disable', defaults: ['toggleTo' => false], name: 'console_organization_community_automation_disable', methods: ['GET'])]
    #[Route('/{uuid}/enable', defaults: ['toggleTo' => true], name: 'console_organization_community_automation_enable', methods: ['GET'])]
    public function toggle(EntityManagerInterface $manager, Request $request, EmailAutomation $automation, bool $toggleTo)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameOrganization($automation);

        $automation->setEnabled($toggleTo);

        $manager->persist($automation);
        $manager->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_organization_community_automation', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
