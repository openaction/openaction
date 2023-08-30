<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Controller\AbstractController;
use App\Entity\Integration\TelegramApp;
use App\Form\Integration\Model\TelegramAppData;
use App\Form\Integration\TelegramAppType;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\Integration\TelegramAppRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/integrations/telegram')]
class TelegramController extends AbstractController
{
    #[Route('', name: 'console_organization_integrations_telegram')]
    public function index(TelegramAppRepository $repository)
    {
        $orga = $this->getOrganization();
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_TELEGRAM);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);

        return $this->render('console/organization/integrations/telegram/index.html.twig', [
            'apps' => $repository->findBy(['organization' => $orga], ['botUsername' => 'ASC']),
        ]);
    }

    #[Route('/register', name: 'console_organization_integrations_telegram_register')]
    public function register(EntityManagerInterface $em, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_TELEGRAM);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);

        $data = new TelegramAppData();

        $form = $this->createForm(TelegramAppType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $app = new TelegramApp($orga, ltrim($data->botUsername, '@'));

            $em->persist($app);
            $em->flush();

            return $this->redirectToRoute('console_organization_integrations_telegram_details', [
                'organizationUuid' => $orga->getUuid(),
                'uuid' => $app->getUuid(),
            ]);
        }

        return $this->render('console/organization/integrations/telegram/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/details', name: 'console_organization_integrations_telegram_details')]
    public function details(TelegramApp $app)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($app);
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_TELEGRAM);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        return $this->render('console/organization/integrations/telegram/details.html.twig', [
            'telegramApp' => $app,
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_organization_integrations_telegram_delete')]
    public function delete(EntityManagerInterface $em, TelegramApp $app, Request $request)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($app);
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_TELEGRAM);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        $em->remove($app);
        $em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        $this->addFlash('success', 'integrations.updated_success');

        return $this->redirectToRoute('console_organization_integrations_telegram', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
