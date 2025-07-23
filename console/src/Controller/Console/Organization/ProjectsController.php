<?php

namespace App\Controller\Console\Organization;

use App\Controller\AbstractController;
use App\Dashboard\DashboardBuilder;
use App\DataManager\ProjectDataManager;
use App\Form\Organization\CreateBatchProjectType;
use App\Form\Organization\CreateProjectType;
use App\Form\Organization\Model\CreateBatchProjectData;
use App\Form\Organization\Model\CreateBatchProjectItemData;
use App\Form\Organization\Model\CreateProjectData;
use App\Platform\Permissions;
use App\Search\Consumer\ReindexOrganizationCrmMessage;
use App\Security\Registration\InviteManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}')]
class ProjectsController extends AbstractController
{
    private array $defaultModules;
    private array $defaultTools;

    public function __construct(
        private readonly MessageBusInterface $bus,
        string $defaultModules,
        string $defaultTools,
    ) {
        $this->defaultModules = explode(',', $defaultModules);
        $this->defaultTools = explode(',', $defaultTools);
    }

    #[Route('/projects', name: 'console_organization_projects')]
    public function list(DashboardBuilder $dashboardBuilder)
    {
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        return $this->render('console/organization/projects/list.html.twig', [
            'dashboard' => $dashboardBuilder->createOrganizationDashboard($this->getOrganization(), $this->getUser()),
        ]);
    }

    #[Route('/project/create', name: 'console_project_create')]
    public function create(ProjectDataManager $dataManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        if ($orga->getProjects()->count() >= $orga->getProjectsSlots()) {
            $response = $this->render('console/subscription/not_enough_slots.html.twig');
            $response->setStatusCode(Response::HTTP_PAYMENT_REQUIRED);

            return $response;
        }

        $data = new CreateProjectData($this->defaultModules, $this->defaultTools);

        $form = $this->createForm(CreateProjectType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $dataManager->createDefault(
                $orga,
                $data->name,
                array_values((array) $data->modules),
                array_values((array) $data->tools),
                'local' === $data->type ? $data->parseAreasIds() : [],
                'thematic' === $data->type ? $data->parseTags() : [],
            );

            // Reindex contacts following projects update
            $this->bus->dispatch(new ReindexOrganizationCrmMessage($orga->getId()));

            return $this->redirectToRoute('console_project_home_start', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/organization/projects/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/create-batch', name: 'console_project_create_batch')]
    public function createBatch(ProjectDataManager $dataManager, InviteManager $inviteManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_PROJECT_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        if ($orga->getProjects()->count() >= $orga->getProjectsSlots()) {
            $response = $this->render('console/subscription/not_enough_slots.html.twig');
            $response->setStatusCode(Response::HTTP_PAYMENT_REQUIRED);

            return $response;
        }

        $data = new CreateBatchProjectData();
        for ($i = 0; $i < 10; ++$i) {
            $data->items[] = new CreateBatchProjectItemData();
        }

        $form = $this->createForm(CreateBatchProjectType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($data->items as $item) {
                if (!$item->name) {
                    continue;
                }

                $project = $dataManager->createDefault(
                    $orga,
                    $item->name,
                    $this->defaultModules,
                    $this->defaultTools,
                    'local' === $item->type ? $item->parseAreasIds() : [],
                );

                // Invite project admin if provided
                if ($item->adminEmail) {
                    $permissions = [];
                    foreach (Permissions::allForProjects() as $permission) {
                        $permissions[$permission] = true;
                    }

                    $inviteManager->invite(
                        $orga,
                        $this->getUser(),
                        $item->adminEmail,
                        false,
                        [$project->getUuid()->toRfc4122() => $permissions],
                        [$project->getUuid()->toRfc4122() => ['posts' => null, 'pages' => null, 'trombinoscope' => null]],
                        $this->getUser()->getLocale()
                    );
                }
            }

            // Reindex contacts following projects update
            $this->bus->dispatch(new ReindexOrganizationCrmMessage($orga->getId()));

            return $this->redirectToRoute('console_organization_projects', [
                'organizationUuid' => $orga->getUuid(),
            ]);
        }

        return $this->render('console/organization/projects/createBatch.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
