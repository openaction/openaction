<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Community\Printing\PrintingPriceCalculator;
use App\Controller\AbstractController;
use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Platform\Permissions;
use App\Repository\Community\PrintingOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/printing')]
class IndexController extends AbstractController
{
    private PrintingOrderRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(PrintingOrderRepository $r, EntityManagerInterface $em)
    {
        $this->repository = $r;
        $this->em = $em;
    }

    #[Route('', name: 'console_community_printing')]
    public function index(PrintingPriceCalculator $priceCalculator, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $project);
        $this->denyIfSubscriptionExpired();

        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/community/printing/index.html.twig', [
            'current_page' => $currentPage,
            'price_calculator' => $priceCalculator,
            'orders_drafts' => $this->repository->findAllDrafts($project),
            'orders_ordered' => $this->repository->findAllOrderedPaginator($project, $currentPage),
            'items_per_page' => 30,
        ]);
    }

    #[Route('/{uuid}/view', name: 'console_community_printing_view', methods: ['GET'])]
    public function view(PrintingPriceCalculator $priceCalculator, PrintingOrder $order)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($order);

        return $this->render('console/project/community/printing/view.html.twig', [
            'order' => $order,
            'price' => $priceCalculator->computePriceEstimate($order),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_community_printing_delete', methods: ['GET'])]
    public function delete(PrintingOrder $order, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($order);

        // Deleting an ordered campaign is forbidden
        if (($order->getCampaigns()->count() > 0 && $order->allBatValidated())
            || ($order->getOrder() && $order->getOrder()->getPaidAt())) {
            throw $this->createAccessDeniedException();
        }

        $this->em->remove($order);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_printing', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/product-delete', name: 'console_community_printing_product_delete', methods: ['GET'])]
    public function productDelete(PrintingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($campaign->getPrintingOrder());

        // Deleting an ordered campaign is forbidden
        if ($campaign->getPrintingOrder()->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        $this->em->remove($campaign);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse([
                'success' => true,
                'redirect' => $this->generateUrl('console_community_printing', ['projectUuid' => $this->getProject()->getUuid()]),
            ]);
        }

        return $this->redirectToRoute('console_community_printing', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
