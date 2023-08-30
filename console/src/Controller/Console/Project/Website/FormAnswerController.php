<?php

namespace App\Controller\Console\Project\Website;

use App\Community\ImportExport\FormAnswerExporter;
use App\Controller\AbstractController;
use App\Entity\Website\Form;
use App\Entity\Website\FormAnswer;
use App\Platform\Permissions;
use App\Repository\Website\FormAnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/forms/results')]
class FormAnswerController extends AbstractController
{
    private EntityManagerInterface $em;
    private FormAnswerRepository $repository;

    public function __construct(EntityManagerInterface $em, FormAnswerRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    #[Route('/{uuid}', name: 'console_website_forms_results')]
    public function results(Form $form, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_ACCESS_RESULTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/website/form/answer/results.html.twig', [
            'form' => $form,
            'answers' => $this->repository->getPaginator($form, $currentPage),
            'current_page' => $currentPage,
        ]);
    }

    #[Route('/{uuid}/export', name: 'console_website_forms_export')]
    public function export(FormAnswerExporter $exporter, Form $form)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_ACCESS_RESULTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($form);

        $response = new StreamedResponse(static function () use ($exporter, $form) {
            $exporter->export($form);
        });

        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                date('Y-m-d').'-'.$form->getSlug().'-answers.xlsx'
            )
        );

        return $response;
    }

    #[Route('/{uuid}/view', name: 'console_website_forms_results_view')]
    public function view(FormAnswer $answer)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_ACCESS_RESULTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($answer->getForm());

        return $this->render('console/project/website/form/answer/view.html.twig', [
            'answer' => $answer,
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_forms_results_delete', methods: ['GET'])]
    public function delete(FormAnswer $answer, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_FORMS_ACCESS_RESULTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($answer->getForm());

        $this->em->remove($answer);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_forms_results', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $answer->getForm()->getUuid(),
        ]);
    }
}
