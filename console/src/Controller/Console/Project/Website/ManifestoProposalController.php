<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\Entity\Website\ManifestoProposal;
use App\Entity\Website\ManifestoTopic;
use App\Form\Website\ManifestoProposalType;
use App\Form\Website\Model\ManifestoProposalData;
use App\Platform\Permissions;
use App\Repository\Website\ManifestoProposalRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/manifesto/proposal/{uuid}')]
class ManifestoProposalController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    private EntityManagerInterface $em;
    private ManifestoProposalRepository $repository;

    public function __construct(EntityManagerInterface $em, ManifestoProposalRepository $r)
    {
        $this->em = $em;
        $this->repository = $r;
    }

    #[Route('/sort', name: 'console_website_manifesto_sort_proposals', methods: ['POST'])]
    public function proposalsSort(ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $data = Json::decode($request->request->get('data'));

        if (0 === count($data)) {
            throw new BadRequestHttpException('Invalid payload sort');
        }

        try {
            $this->repository->sort($data);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(['success' => 1]);
    }

    #[Route('/create', name: 'console_website_manifesto_proposal_create')]
    public function proposalCreate(TranslatorInterface $translator, Request $request, ManifestoTopic $topic)
    {
        $project = $this->getProject();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $project);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $proposal = new ManifestoProposal(
            $topic,
            $translator->trans('create.proposal.title', [], 'project_manifesto'),
            1 + $this->repository->count(['topic' => $topic])
        );

        $this->em->persist($proposal);
        $this->em->flush();

        return $this->redirectToRoute('console_website_manifesto_proposal_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $proposal->getUuid(),
        ]);
    }

    #[Route('/edit', name: 'console_website_manifesto_proposal_edit', methods: ['GET'])]
    public function edit(ManifestoProposal $proposal)
    {
        if ($proposal->getTopic()->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($proposal->getTopic());

        return $this->render('console/project/website/manifesto/editProposal.html.twig', [
            'proposal' => $proposal,
            'form' => $this->createForm(ManifestoProposalType::class, new ManifestoProposalData())->createView(),
        ]);
    }

    #[Route('/save', name: 'console_website_manifesto_proposal_save', methods: ['POST'])]
    public function save(ManifestoProposal $proposal, Request $request)
    {
        if ($proposal->getTopic()->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($proposal->getTopic());

        $data = new ManifestoProposalData();

        $form = $this->createForm(ManifestoProposalType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $proposal->applyUpdate($data);

        $this->em->persist($proposal);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/upload', name: 'console_website_manifesto_proposal_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, ManifestoProposal $proposal, Request $request)
    {
        if ($proposal->getTopic()->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($proposal->getTopic());

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($proposal->getTopic()->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/duplicate', name: 'console_website_manifesto_proposal_duplicate', methods: ['GET'])]
    public function duplicate(ManifestoProposal $proposal, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($proposal->getTopic());

        $this->em->persist($duplicated = $proposal->duplicate());
        $this->em->flush();

        return $this->redirectToRoute('console_website_manifesto_proposal_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/delete', name: 'console_website_manifesto_proposal_delete', methods: ['GET'])]
    public function delete(ManifestoProposal $proposal, Request $request)
    {
        if ($proposal->getTopic()->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($proposal->getTopic());

        $this->em->remove($proposal);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_manifesto', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
