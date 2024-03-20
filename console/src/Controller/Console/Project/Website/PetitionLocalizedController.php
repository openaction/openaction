<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\Entity\Website\PetitionLocalized;
use App\Form\Website\Model\PetitionLocalizedData;
use App\Form\Website\Model\PetitionLocalizedImageData;
use App\Form\Website\Model\PetitionLocalizedLocaleData;
use App\Form\Website\PetitionLocalizedImageType;
use App\Form\Website\PetitionLocalizedLocaleType;
use App\Form\Website\PetitionLocalizedType;
use App\Platform\Permissions;
use App\Repository\Website\PetitionCategoryRepository;
use App\Repository\Website\PetitionLocalizedRepository;
use App\Repository\Website\PetitionRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/petition_localized')]
class PetitionLocalizedController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    public function __construct(
        private readonly PetitionCategoryRepository $categoryRepository,
        private readonly PetitionRepository $petitionRepository,
        private readonly PetitionLocalizedRepository $repository,
        private readonly TrombinoscopePersonRepository $trombinoscopePersonRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/pre_create', name: 'console_website_petition_localized_pre_create', methods: ['GET'])]
    public function preCreate(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $petitionUuid = $request->query->get('petitionUuid') ?: $request->query->getIterator()->getArrayCopy()['petition_localized_locale']['petitionUuid'];
        $petition = $this->petitionRepository->findOneBy(['uuid' => $petitionUuid]);

        if (!$petition) {
            return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        $data = new PetitionLocalizedLocaleData($petitionUuid);
        $form = $this->createForm(PetitionLocalizedLocaleType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('console_website_petition_localized_create', [
                'projectUuid' => $this->getProject()->getUuid(),
                'petitionUuid' => $petition->getUuid(),
                'locale' => $data->locale,
            ]);
        }

        return $this->render('console/project/website/petition_localized/pre_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'console_website_petition_localized_create', methods: ['GET'])]
    public function create(Request $request, TranslatorInterface $translator): RedirectResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $petition = $this->petitionRepository->findOneBy(['uuid' => $request->query->get('petitionUuid')]);

        if (!$petition) {
            return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        $entity = new PetitionLocalized(
            $petition,
            $translator->trans('create.title', [], 'project_petitions'),
            $translator->trans('create.form.title', [], 'project_petitions'),
            $request->query->get('locale')
        );

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirectToRoute('console_website_petition_localized_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $entity->getUuid(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_petition_localized_edit', methods: ['GET'])]
    public function edit(PetitionLocalized $petitionLocalized): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_ENTITY, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return $this->render('console/project/website/petition_localized/edit.html.twig', [
            'petitionLocalized' => $petitionLocalized,
            'form' => $this->createForm(PetitionLocalizedType::class, new PetitionLocalizedData())->createView(),
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
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_ENTITY, $petitionLocalized->getPetition());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        $petitionLocalizedData = new PetitionLocalizedData();

        $form = $this->createForm(PetitionLocalizedType::class, $petitionLocalizedData, ['validation_groups' => $groupValidation]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ('Default' === $groupValidation) {
            $petitionLocalized->applyContentUpdate($petitionLocalizedData);

            // update petition slug if localized is the default website locale
            if ($petitionLocalized->getLocale() === $this->getProject()->getWebsiteLocale()) {
                $petition = $petitionLocalized->getPetition();
                $petition->setSlug((new AsciiSlugger())->slug($petitionLocalizedData->title)->lower());

                $this->em->persist($petition);
                $this->em->flush();
            }
        } elseif ('Metadata' === $groupValidation) {
            $petitionLocalized->applyMetadataUpdate($petitionLocalizedData);
        }

        $this->em->persist($petitionLocalized);
        $this->em->flush();

        if ('Metadata' === $groupValidation) {
            $this->categoryRepository->updateCategories($petitionLocalized, $petitionLocalizedData->getCategoriesArray());
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/{uuid}/content/upload', name: 'console_website_petition_localized_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, PetitionLocalized $petitionLocalized, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_ENTITY, $petitionLocalized);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($petitionLocalized->getPetition()->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/{uuid}/update/image', name: 'console_website_petition_localized_update_image')]
    public function updateImage(PetitionLocalized $petitionLocalized, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_ENTITY, $petitionLocalized->getPetition());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        $data = new PetitionLocalizedImageData();

        $form = $this->createForm(PetitionLocalizedImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->replaceImage($petitionLocalized, $uploader->upload(
            CdnUploadRequest::createWebsiteContentMainImageRequest($petitionLocalized->getPetition()->getProject(), $data->file)
        ));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($petitionLocalized->getImage())]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_petition_localized_delete', methods: ['GET'])]
    public function delete(PetitionLocalized $petitionLocalized, Request $request): RedirectResponse|JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_ENTITY, $petitionLocalized->getPetition());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        $this->em->remove($petitionLocalized);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'redirect' => $this->generateUrl('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]),
        ]);
    }
}
