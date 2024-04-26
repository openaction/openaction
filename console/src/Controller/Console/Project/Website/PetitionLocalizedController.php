<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\Entity\Website\PetitionLocalized;
use App\Form\Website\Model\PetitionLocalizedData;
use App\Form\Website\Model\PetitionLocalizedImageData;
use App\Form\Website\PetitionLocalizedImageType;
use App\Form\Website\PetitionLocalizedType;
use App\Platform\Permissions;
use App\Repository\Website\PetitionCategoryRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petition_localized')]
class PetitionLocalizedController extends AbstractController
{
    use ContentEditorUploadControllerTrait;

    public function __construct(
        private readonly TrombinoscopePersonRepository $trombinoscopePersonRepository,
        private readonly PetitionCategoryRepository $categoryRepository,
    ) {
    }

    #[Route('/{uuid}/edit', name: 'console_website_petition_localized_edit', methods: ['GET'])]
    public function edit(PetitionLocalized $petitionLocalized): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return $this->render('console/project/website/petition_localized/edit.html.twig', [
            'petitionLocalized' => $petitionLocalized,
            'form' => $this->createForm(PetitionLocalizedType::class, new PetitionLocalizedData())->createView(),
            'availableAuthors' => $this->trombinoscopePersonRepository->getProjectPersonsList($this->getProject(), AbstractQuery::HYDRATE_ARRAY),
            'categories' => $this->categoryRepository->getPetitionCategoriesForProject($this->getProject(), AbstractQuery::HYDRATE_ARRAY),
            'image_form' => $this->createForm(PetitionLocalizedImageType::class, new PetitionLocalizedImageData())->createView(),
        ]);
    }

    #[Route('/{uuid}/update/content', name: 'console_website_petition_localized_update_content', methods: ['POST'])]
    public function updateContent(PetitionLocalized $petitionLocalized, Request $request): JsonResponse
    {
        return $this->update($petitionLocalized, $request, 'Default');
    }

    #[Route('/{uuid}/update/metadata', name: 'console_website_petition_localized_update_metadata', methods: ['POST'])]
    public function updateMetadata(PetitionLocalized $petitionLocalized, Request $request): JsonResponse
    {
        return $this->update($petitionLocalized, $request, 'Metadata');
    }

    private function update(PetitionLocalized $petitionLocalized, Request $request, string $groupValidation): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petitionLocalized);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{uuid}/content/upload', name: 'console_website_petition_localized_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, PetitionLocalized $petitionLocalized, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petitionLocalized);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($petitionLocalized->getPetition()->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/{uuid}/update/image', name: 'console_website_petition_localized_update_image')]
    public function updateImage(PetitionLocalized $petitionLocalized, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petitionLocalized);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_petition_localized_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, PetitionLocalized $petitionLocalized, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}