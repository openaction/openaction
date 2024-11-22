<?php

namespace App\Controller\Console\Project\Community\Emailing;

use App\Bridge\Unlayer\UnlayerInterface;
use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\EmailingCampaign;
use App\Form\Community\EmailingCampaignMetaDataType;
use App\Form\Community\Model\EmailingCampaignMetaData;
use App\Platform\Permissions;
use App\Repository\Community\EmailingCampaignRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/community/emailing')]
class IndexController extends AbstractController
{
    use ApiControllerTrait;

    private EmailingCampaignRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(EmailingCampaignRepository $r, EntityManagerInterface $em)
    {
        $this->repository = $r;
        $this->em = $em;
    }

    #[Route('', name: 'console_community_emailing')]
    public function index(Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $project);
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/community/emailing/index.html.twig', [
            'project' => $project,
            'current_page' => $currentPage,
            'campaigns_drafts' => $this->repository->findAllDrafts($project),
            'campaigns_sent' => $this->repository->findAllSentPaginator($project, $currentPage),
            'items_per_page' => 10,
        ]);
    }

    #[Route('/create-from-template/{templateId}', defaults: ['templateId' => null], name: 'console_community_emailing_create_template')]
    public function createFromTemplate(UnlayerInterface $unlayer, TranslatorInterface $translator, Request $request, ?string $templateId)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $templates = $unlayer->getEmailingTemplates();

        if ($templateId) {
            $this->denyUnlessValidCsrf($request);

            if (!$template = $templates[$templateId] ?? null) {
                throw $this->createNotFoundException();
            }

            $campaign = new EmailingCampaign(
                $this->getProject(),
                $translator->trans('create.subject', [], 'project_emailings'),
                'no-reply',
                $translator->trans('create.fromName', [
                    '%user%' => $this->getUser()->getFirstName(),
                    '%organization%' => $this->getProject()->getOrganization()->getName(),
                ], 'project_emailings')
            );

            $campaign->applyUnlayerUpdate($template['design'], '');

            $this->em->persist($campaign);
            $this->em->flush();

            return $this->redirectToRoute('console_community_emailing_content', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $campaign->getUuid(),
            ]);
        }

        return $this->render('console/project/community/emailing/create_template.html.twig', [
            'templates' => $templates,
        ]);
    }

    #[Route('/duplicate/{uuid}', name: 'console_community_emailing_duplicate')]
    public function create(TranslatorInterface $translator, Request $request, EmailingCampaign $toDuplicate)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $campaign = $toDuplicate->duplicate();

        $this->em->persist($campaign);
        $this->em->flush();

        return $this->redirectToRoute('console_community_emailing_content', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $campaign->getUuid(),
        ]);
    }

    #[Route('/{uuid}/metadata', name: 'console_community_emailing_metadata', methods: ['GET', 'POST'])]
    public function metadata(EmailingCampaignRepository $repo, EmailingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        // Already sent campaigns can't be edited anymore
        if ($campaign->getSentAt()) {
            throw $this->createAccessDeniedException();
        }

        $metadata = EmailingCampaignMetaData::createFromCampaign($campaign);

        $form = $this->createForm(EmailingCampaignMetaDataType::class, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->applyMetadataUpdate($metadata);
            $repo->updateFilters($campaign, $metadata);

            $this->em->persist($campaign);
            $this->em->flush();

            $this->addFlash('success', 'emailings.metadata_updated_success');

            if (!$campaign->getContent()) {
                return $this->redirectToRoute('console_community_emailing_content', [
                    'projectUuid' => $this->getProject()->getUuid(),
                    'uuid' => $campaign->getUuid(),
                ]);
            }

            return $this->redirectToRoute('console_community_emailing_metadata', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $campaign->getUuid(),
            ]);
        }

        return $this->render('console/project/community/emailing/metadata.html.twig', [
            'campaign' => $campaign,
            'project' => $this->getProject(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/content', name: 'console_community_emailing_content', methods: ['GET'])]
    public function content(EmailingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        return $this->render('console/project/community/emailing/content.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{uuid}/content/update', name: 'console_community_emailing_content_update', methods: ['POST'])]
    public function updateContent(EmailingCampaign $campaign, Request $request)
    {
        // Already sent campaigns can't be edited anymore
        if ($campaign->getSentAt()) {
            throw $this->createAccessDeniedException();
        }

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $content = $request->request->get('content');
        $design = $request->request->get('design');

        if ($design) {
            $campaign->applyUnlayerUpdate(Json::decode($design), $content);
        } else {
            $campaign->applyContentUpdate($content);
        }

        $this->em->persist($campaign);
        $this->em->flush();

        return new JsonResponse(['success' => true], 200);
    }

    #[Route('/{uuid}/content/upload', name: 'console_community_emailing_content_upload_images', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, EmailingCampaign $campaign, Request $request)
    {
        // Already sent campaigns can't be edited anymore
        if ($campaign->getSentAt()) {
            throw $this->createAccessDeniedException();
        }

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        if (!$uploadedFile = $request->files->get('file')) {
            throw $this->createAccessDeniedException();
        }

        if ($uploadedFile->getSize() > 26214400) { // 25 MiB
            throw new BadRequestHttpException('File too large.');
        }

        if (!str_starts_with($uploadedFile->getMimeType(), 'image/')) {
            throw new BadRequestHttpException('Invalid image.');
        }

        $upload = $uploader->upload(CdnUploadRequest::createEmailingContentRequest($campaign->getProject(), $uploadedFile));

        return new JsonResponse(['url' => $router->generateUrl($upload)]);
    }

    #[Route('/{uuid}/delete', name: 'console_community_emailing_delete', methods: ['GET'])]
    public function delete(Request $request, EmailingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($campaign);

        $this->em->remove($campaign);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_emailing', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
