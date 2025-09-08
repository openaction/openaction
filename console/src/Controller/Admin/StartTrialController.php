<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\DataManager\ProjectDataManager;
use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Form\Admin\Model\StartTrialData;
use App\Form\Admin\StartTrialType;
use App\Search\CrmIndexer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class StartTrialController extends AbstractController
{
    public function __construct(
        private string $defaultPlan,
        private string $defaultModules,
        private string $defaultTools,
        private ProjectDataManager $dataManager,
        private EntityManagerInterface $em,
        private CrmIndexer $crmIndexer,
    ) {
    }

    #[Route('/start-trial', name: 'admin_start_trial')]
    public function startTrial(Request $request)
    {
        $data = new StartTrialData();
        $data->plan = $this->defaultPlan;

        $form = $this->createForm(StartTrialType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create organization
            $organization = Organization::createTrialing($data->name, $data->plan);
            $orgaUuid = $organization->getUuid()->toRfc4122();

            $this->em->persist($organization);
            $this->em->persist($member = new OrganizationMember($organization, $this->getUser(), true));
            $this->em->flush();

            $organization->getMembers()->add($member);

            // Create CRM index and API tokens
            $indexVersion = $this->crmIndexer->createIndexVersion($orgaUuid);
            $this->crmIndexer->bumpIndexVersion($orgaUuid, $indexVersion);

            // Create initial project
            $this->dataManager->createDefault(
                $organization,
                $organization->getName(),
                explode(',', $this->defaultModules),
                explode(',', $this->defaultTools),
                [],
                [],
                false,
            );

            return $this->redirectToRoute('console_organization_projects', [
                'organizationUuid' => $organization->getUuid(),
            ]);
        }

        return $this->render('admin/start_trial.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
