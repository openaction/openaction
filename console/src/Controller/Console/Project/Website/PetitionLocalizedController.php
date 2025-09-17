<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\Entity\Website\LocalizedPetition;
use App\Entity\Website\Petition;
use App\Form\Website\LocalizedPetitionImageType;
use App\Form\Website\LocalizedPetitionType;
use App\Form\Website\Model\LocalizedPetitionData;
use App\Form\Website\Model\LocalizedPetitionImageData;
use App\Form\Website\Model\PetitionData;
use App\Form\Website\PetitionType;
use App\Platform\Permissions;
use App\Repository\Website\LocalizedPetitionRepository;
use App\Repository\Website\PetitionCategoryRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petitions')]
class PetitionLocalizedController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LocalizedPetitionRepository $localizedRepository,
        private readonly PetitionCategoryRepository $categoryRepository,
        private readonly TrombinoscopePersonRepository $trombinoscopePersonRepository,
    ) {
    }

    #[Route('/localized/{uuid}/edit', name: 'console_website_petition_localized_edit', methods: ['GET'])]
    public function edit(LocalizedPetition $localized)
    {
        $petition = $localized->getPetition();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petition->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        return $this->render('console/project/website/petition_localized/edit.html.twig', [
            'petition' => $petition,
            'localized' => $localized,
            'categories' => $this->categoryRepository->getProjectCategories($this->getProject(), Query::HYDRATE_ARRAY),
            'form' => $this->createForm(LocalizedPetitionType::class, new LocalizedPetitionData())->createView(),
            'image_form' => $this->createForm(LocalizedPetitionImageType::class, new LocalizedPetitionImageData())->createView(),
            'parent_form' => $this->createForm(PetitionType::class, new PetitionData())->createView(),
            'availableAuthors' => $this->trombinoscopePersonRepository->getProjectPersonsList($this->getProject(), Query::HYDRATE_ARRAY),
        ]);
    }

    #[Route('/localized/{uuid}/update/content', name: 'console_website_petition_localized_update_content', methods: ['POST'])]
    public function updateContent(LocalizedPetition $localized, Request $request)
    {
        return $this->applyUpdate($localized, $request, 'Default');
    }

    #[Route('/localized/{uuid}/update/metadata', name: 'console_website_petition_localized_update_metadata', methods: ['POST'])]
    public function updateMetadata(LocalizedPetition $localized, Request $request)
    {
        return $this->applyUpdate($localized, $request, 'Metadata');
    }

    private function applyUpdate(LocalizedPetition $localized, Request $request, string $groupValidation)
    {
        $petition = $localized->getPetition();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petition->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        $data = new LocalizedPetitionData();
        $form = $this->createForm(LocalizedPetitionType::class, $data, ['validation_groups' => $groupValidation]);
        $form->submit($request->request->all($form->getName()), false);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ('Default' === $groupValidation) {
            $localized->applyContentUpdate($data);
        } elseif ('Metadata' === $groupValidation) {
            $localized->applyMetadataUpdate($data);
        }

        $this->em->persist($localized);
        $this->em->flush();

        if ('Metadata' === $groupValidation) {
            $this->categoryRepository->updateCategoriesForLocalized($localized, $data->getCategoriesArray());
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/localized/{uuid}/update/parent', name: 'console_website_petition_localized_update_parent', methods: ['POST'])]
    public function updateParent(LocalizedPetition $localized, Request $request)
    {
        $petition = $localized->getPetition();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petition->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        $data = new PetitionData();
        $form = $this->createForm(PetitionType::class, $data);
        $form->submit($request->request->all($form->getName()), false);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (null !== $data->slug) {
            $petition->setSlug($data->slug);
        }
        $petition->setStartAt($data->startAt ? new \DateTime($data->startAt) : null);
        $petition->setEndAt($data->endAt ? new \DateTime($data->endAt) : null);
        $petition->setSignaturesGoal($data->signaturesGoal);
        if (null !== $data->publishedAt) {
            $petition->setPublishedAt($data->publishedAt ? new \DateTime($data->publishedAt) : null);
        }

        $this->em->persist($petition);
        $this->em->flush();

        $this->trombinoscopePersonRepository->updateAuthorsForPetition($petition, $data->getAuthorsArray());

        return new JsonResponse(['success' => true]);
    }

    #[Route('/localized/{uuid}/update/image', name: 'console_website_petition_localized_update_image', methods: ['POST'])]
    public function updateImage(LocalizedPetition $localized, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request)
    {
        $petition = $localized->getPetition();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petition->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        $data = new LocalizedPetitionImageData();
        $form = $this->createForm(LocalizedPetitionImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->localizedRepository->replaceImage($localized, $uploader->upload(
            CdnUploadRequest::createWebsiteContentMainImageRequest($petition->getProject(), $data->file)
        ));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($localized->getImage())]);
    }

    #[Route('/localized/{uuid}/content/upload', name: 'console_website_petition_localized_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, LocalizedPetition $localized, Request $request)
    {
        $petition = $localized->getPetition();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $petition->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($petition->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/{uuid}/localized/{locale}/create', name: 'console_website_petition_localized_create', methods: ['GET'])]
    public function create(Petition $petition, string $locale, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        foreach ($petition->getLocalizations() as $loc) {
            if ($loc->getLocale() === $locale) {
                return $this->redirectToRoute('console_website_petition_localized_edit', [
                    'projectUuid' => $this->getProject()->getUuid(),
                    'uuid' => $loc->getUuid(),
                ]);
            }
        }

        $localized = new LocalizedPetition($petition, $locale, '');
        $this->em->persist($localized);
        $this->em->flush();

        return $this->redirectToRoute('console_website_petition_localized_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $localized->getUuid(),
        ]);
    }
}
