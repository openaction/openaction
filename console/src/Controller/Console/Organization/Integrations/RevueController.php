<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Controller\AbstractController;
use App\Entity\Integration\RevueAccount;
use App\Form\Integration\Model\RevueAccountData;
use App\Form\Integration\RevueAccountType;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\Integration\RevueAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/integrations/revue')]
class RevueController extends AbstractController
{
    #[Route('', name: 'console_organization_integrations_revue')]
    public function index(RevueAccountRepository $repository)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_REVUE);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        return $this->render('console/organization/integrations/revue/index.html.twig', [
            'accounts' => $repository->findBy(['organization' => $this->getOrganization()], ['label' => 'ASC']),
        ]);
    }

    #[Route('/connect', name: 'console_organization_integrations_revue_connect')]
    public function connect(EntityManagerInterface $em, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_REVUE);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);

        $data = new RevueAccountData();

        $form = $this->createForm(RevueAccountType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account = new RevueAccount($orga, $data->label, $data->apiToken);

            $em->persist($account);
            $em->flush();

            $this->addFlash('success', 'integrations.updated_success');

            return $this->redirectToRoute('console_organization_integrations_revue', [
                'organizationUuid' => $orga->getUuid(),
            ]);
        }

        return $this->render('console/organization/integrations/revue/connect.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_organization_integrations_revue_edit')]
    public function edit(EntityManagerInterface $em, RevueAccount $account, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_REVUE);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);

        $data = RevueAccountData::createFromAccount($account);

        $form = $this->createForm(RevueAccountType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account->applyDataUpdate($data);

            $em->persist($account);
            $em->flush();

            $this->addFlash('success', 'integrations.updated_success');

            return $this->redirectToRoute('console_organization_integrations_revue', [
                'organizationUuid' => $orga->getUuid(),
            ]);
        }

        return $this->render('console/organization/integrations/revue/edit.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_organization_integrations_revue_delete')]
    public function delete(EntityManagerInterface $em, RevueAccount $account, Request $request)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($account);
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_REVUE);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        $em->remove($account);
        $em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        $this->addFlash('success', 'integrations.updated_success');

        return $this->redirectToRoute('console_organization_integrations_revue', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
