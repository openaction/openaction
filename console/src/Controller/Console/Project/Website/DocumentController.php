<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Website\Document;
use App\Form\Website\DocumentType;
use App\Form\Website\Model\DocumentData;
use App\Platform\Permissions;
use App\Repository\Website\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/documents')]
class DocumentController extends AbstractController
{
    use ApiControllerTrait;

    private DocumentRepository $repository;
    private EntityManagerInterface $em;
    private RequestStack $requestStack;

    public function __construct(DocumentRepository $repository, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    #[Route('', name: 'console_website_documents')]
    public function index(Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::WEBSITE_DOCUMENTS_MANAGE, $project);
        $this->denyIfSubscriptionExpired();

        if ($this->requestStack->getSession()->has('file_upload')) {
            if (1 === $this->requestStack->getSession()->get('file_upload')) {
                $this->addFlash('success', 'documents.upload_success.single');
            } else {
                $this->addFlash('success', 'documents.upload_success.plural');
            }

            $this->requestStack->getSession()->remove('file_upload');
        }

        return $this->render('console/project/website/document/index.html.twig', [
            'documents' => $this->repository->getProjectDocuments($project),
            'project' => $project,
            'form' => $this->createForm(DocumentType::class, new DocumentData())->createView(),
        ]);
    }

    #[Route('/create', name: 'console_website_document_create', methods: ['POST'])]
    public function create(Request $request, CdnUploader $uploader)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::WEBSITE_DOCUMENTS_MANAGE, $project);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $data = new DocumentData();

        $form = $this->createForm(DocumentType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $upload = $uploader->upload(CdnUploadRequest::createWebsiteDocumentRequest($project, $data->file));
        $document = Document::createFromData($project, $data, $upload);

        $this->em->persist($document);
        $this->em->flush();

        if ($this->requestStack->getSession()->has('file_upload')) {
            $this->requestStack->getSession()->set('file_upload', $this->requestStack->getSession()->get('file_upload') + 1);
        } else {
            $this->requestStack->getSession()->set('file_upload', 1);
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_document_delete', methods: ['GET'])]
    public function delete(Document $document, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::WEBSITE_DOCUMENTS_MANAGE, $project);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($document);

        $this->em->remove($document);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_documents', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/download', name: 'console_website_document_download', methods: ['GET'])]
    public function download(Document $document, FilesystemReader $cdnStorage)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_DOCUMENTS_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($document);

        $path = $document->getFile()->getPathname();
        $response = new StreamedResponse(
            static function () use ($cdnStorage, $path) {
                stream_copy_to_stream($cdnStorage->readStream($path), fopen('php://output', 'wb'));
            }
        );

        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $document->getName());

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
