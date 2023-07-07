<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\DataManager\FormDataManager;
use App\Entity\Website\Form;
use App\Entity\Website\FormBlock;
use App\Form\Project\Model\MoveEntityData;
use App\Form\Project\MoveEntityType;
use App\Form\Website\Model\FormData;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\Website\FormRepository;
use App\Util\Json;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/forms')]
class FormController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    private EntityManagerInterface $em;
    private FormRepository $repository;

    public function __construct(EntityManagerInterface $em, FormRepository $r)
    {
        $this->em = $em;
        $this->repository = $r;
    }

    #[Route('', name: 'console_website_forms')]
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();
        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/website/form/index.html.twig', [
            'forms' => $this->repository->getPaginator($project, $currentPage),
            'current_page' => $currentPage,
            'project' => $project,
        ]);
    }

    #[Route('/create', name: 'console_website_forms_create')]
    public function create(TranslatorInterface $translator, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $form = new Form(
            $this->getProject(),
            $translator->trans('create.title', [], 'project_forms'),
            1 + $this->repository->count(['project' => $this->getProject()])
        );

        $this->em->persist($form);
        $this->em->persist(new FormBlock($form, FormBlock::TYPE_EMAIL, $translator->trans('create.question_email', [], 'project_forms')));
        $this->em->flush();

        return $this->redirectToRoute('console_website_forms_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $form->getUuid(),
        ]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_website_forms_duplicate', methods: ['GET'])]
    public function duplicate(FormDataManager $dataManager, Request $request, Form $form)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        $duplicated = $dataManager->duplicate($form);

        return $this->redirectToRoute('console_website_forms_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/move', name: 'console_website_forms_move', methods: ['GET', 'POST'])]
    public function move(FormDataManager $dataManager, Form $formData, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($formData);

        $data = new MoveEntityData();

        $form = $this->createForm(MoveEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_FORMS_MANAGE,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::WEBSITE_FORMS_MANAGE, $data->into)) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($formData, $data->into);

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_website_forms', [
                'projectUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/website/form/move.html.twig', [
            'formData' => $formData,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_forms_edit', methods: ['GET'])]
    public function edit(Form $form, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        return $this->render('console/project/website/form/edit.html.twig', [
            'form' => $form,
            'form_data' => FormData::createFromForm($form)->toArray(),
            'from' => $request->query->get('from'),
        ]);
    }

    #[Route('/{uuid}/update', name: 'console_website_forms_update', methods: ['POST'])]
    public function update(ValidatorInterface $validator, Form $form, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        try {
            $payload = Json::decode($request->getContent());
        } catch (\Throwable) {
            throw new BadRequestHttpException();
        }

        $data = FormData::createFromPayload($payload);

        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException();
        }

        $form->applyUpdate($data);
        $this->em->persist($form);
        $this->em->flush();

        $this->repository->saveBlocks($form, $data);

        return $this->createJsonApiResponse('OK', 200);
    }

    #[Route('/{uuid}/delete', name: 'console_website_forms_delete', methods: ['GET'])]
    public function delete(Form $form, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        $this->em->remove($form);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_forms', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/view', name: 'console_website_forms_view')]
    public function view(DomainRouter $domainRouter, Form $form)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        return $this->redirect($domainRouter->generateRedirectUrl($this->getProject(), 'form', Uid::toBase62($form->getUuid())));
    }
}
