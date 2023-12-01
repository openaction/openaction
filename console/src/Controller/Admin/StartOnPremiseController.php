<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\DataManager\ProjectDataManager;
use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Form\Admin\Model\StartOnPremiseData;
use App\Form\Admin\StartOnPremiseType;
use App\Platform\Plans;
use App\Repository\UserRepository;
use App\Search\CrmIndexer;
use App\Security\Registration\InviteManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class StartOnPremiseController extends AbstractController
{
    public function __construct(
        private ProjectDataManager $dataManager,
        private UserRepository $userRepository,
        private EntityManagerInterface $manager,
        private InviteManager $inviteManager,
        private CrmIndexer $crmIndexer,
        private AdminUrlGenerator $urlGenerator,
    ) {
    }

    #[Route('/start-on-premise', name: 'admin_start_on_premise')]
    public function startOnPremise(Request $request)
    {
        $data = new StartOnPremiseData();

        $form = $this->createForm(StartOnPremiseType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization = Organization::createOnPremise($data->region, Plans::ORGANIZATION);
            $orgaUuid = $organization->getUuid()->toRfc4122();

            if (($defaultPartner = $this->getParameter('default_partner'))
                && $partner = $this->userRepository->findOneByEmail($defaultPartner)) {
                $organization->setPartner($partner);
            }

            $this->manager->persist($organization);
            $this->manager->persist($member = new OrganizationMember($organization, $this->getUser(), true));
            $this->manager->flush();

            $organization->getMembers()->add($member);

            // Create CRM index and API tokens
            $indexVersion = $this->crmIndexer->createIndexVersion($orgaUuid);
            $this->crmIndexer->bumpIndexVersion($orgaUuid, $indexVersion);

            // Create initial projects
            $this->dataManager->createOnPremise($organization, $data);

            // Invite members
            $adminEmail = strtolower($this->getUser()->getEmail());
            if ($adminEmail !== $data->adminEmail) {
                $this->inviteManager->invite($organization, $this->getUser(), $data->adminEmail, true, [], 'fr');
            }

            return $this->redirect($this->urlGenerator->setController(OrganizationController::class)->generateUrl());
        }

        return $this->render('admin/start_on_premise.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
