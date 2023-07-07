<?php

namespace App\Controller\Console\Project\Configuration;

use App\Controller\AbstractController;
use App\DataManager\ProjectDataManager;
use App\Form\Project\Model\MoveProjectData;
use App\Form\Project\Model\UpdateDetailsData;
use App\Form\Project\Model\UpdateLegalitiesData;
use App\Form\Project\Model\UpdateModulesData;
use App\Form\Project\MoveProjectType;
use App\Form\Project\UpdateDetailsType;
use App\Form\Project\UpdateLegalitiesType;
use App\Form\Project\UpdateModulesType;
use App\Platform\Permissions;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/settings')]
class SettingsController extends AbstractController
{
    #[Route('', name: 'console_configuration_settings')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/settings/index.html.twig');
    }

    #[Route('/details', name: 'console_configuration_settings_details')]
    public function details(EntityManagerInterface $em, ProjectRepository $projectRepository, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();
        $data = UpdateDetailsData::createFromProject($project);

        $form = $this->createForm(UpdateDetailsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applyDetailsUpdate($data);
            $em->persist($project);
            $em->flush();

            // Update areas and tags
            $projectRepository->updateAreas($project, $data->parseAreasIds());
            $projectRepository->updateTags($project, $data->parseTags());

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_configuration_settings_details', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/settings/details.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modules', name: 'console_configuration_settings_modules')]
    public function modules(EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();
        $data = UpdateModulesData::createFromProject($project);

        $form = $this->createForm(UpdateModulesType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->updateModules($data->modules, $data->tools);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_configuration_settings_modules', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/settings/modules.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/legalities', name: 'console_configuration_settings_legalities')]
    public function legalities(EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();

        $data = UpdateLegalitiesData::createFromProject($project);

        $form = $this->createForm(UpdateLegalitiesType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applyLegalitiesUpdate($data);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_configuration_settings_legalities', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/settings/legalities.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/domain', name: 'console_configuration_settings_domain')]
    public function domain()
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/settings/domain.html.twig');
    }

    #[Route('/duplicate', name: 'console_configuration_settings_duplicate')]
    public function duplicate(ProjectDataManager $dataManager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if ($this->getOrganization()->getProjects()->count() >= $this->getOrganization()->getProjectsSlots()) {
            $response = $this->render('console/subscription/not_enough_slots.html.twig');
            $response->setStatusCode(Response::HTTP_PAYMENT_REQUIRED);

            return $response;
        }

        $dataManager->duplicate($this->getProject());

        return $this->redirectToRoute('console_organization_projects', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }

    #[Route('/move', name: 'console_configuration_settings_move', methods: ['GET', 'POST'])]
    public function move(ProjectDataManager $dataManager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $data = new MoveProjectData();

        $form = $this->createForm(MoveProjectType::class, $data, [
            'user' => $this->getUser(),
            'current_organization' => $this->getOrganization(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $data->into)) {
                throw $this->createNotFoundException();
            }

            if ($data->into->getProjects()->count() >= $data->into->getProjectsSlots()) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($this->getProject(), $data->into);

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_organization_projects', [
                'organizationUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/settings/move.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/remove', name: 'console_configuration_settings_remove')]
    public function remove(EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        $manager->remove($this->getProject());
        $manager->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_organization_projects', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
