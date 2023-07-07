<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Billing\BillingManager;
use App\Community\Printing\PrintingPriceCalculator;
use App\Community\Printing\PrintingWorkflow;
use App\Controller\AbstractController;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Community\PrintingOrder;
use App\Form\Community\Printing\Model\PrintingOrderBuyData;
use App\Form\Community\Printing\PrintingOrderBuyType;
use App\Platform\Companies;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/console/project/{projectUuid}/community/printing')]
class OrderController extends AbstractController
{
    private PrintingPriceCalculator $priceCalculator;
    private BillingManager $billingManager;
    private PrintingWorkflow $printingWorkflow;

    public function __construct(PrintingPriceCalculator $pc, BillingManager $bm, PrintingWorkflow $pw)
    {
        $this->priceCalculator = $pc;
        $this->billingManager = $bm;
        $this->printingWorkflow = $pw;
    }

    #[Route('/{uuid}/order', name: 'console_community_printing_order')]
    public function order(PrintingOrder $order, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $this->getProject());
        $this->denyIfSubscriptionExpired();

        if (!$order->isReadyToOrder() || $order->getOrder()) {
            throw $this->createNotFoundException();
        }

        $lines = $this->priceCalculator->createOrderLines($order);

        $orga = $this->getOrganization();
        $data = new PrintingOrderBuyData($orga, $this->getUser());

        $form = $this->createForm(PrintingOrderBuyType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist billing details in organization
            $orga->applyBillingDetailsUpdate($data->createUpdateBillingDetailsData());
            $this->billingManager->persistMollieCustomer($orga);

            // Create order
            $recipient = $data->createOrderRecipient($this->getUser()->getLocale());
            $action = OrderAction::print($order->getUuid()->toRfc4122());

            $successUrl = $this->generateUrl(
                'console_community_printing_ordered',
                ['projectUuid' => $this->getProject()->getUuid(), 'uuid' => $order->getUuid()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            if ($order->isSubrogatedOrder()) {
                $billingOrder = $this->billingManager->createManualOrder(Companies::CID, $orga, $recipient, $action, $lines);
                $this->billingManager->markManualOrderPaid($billingOrder);
            } else {
                $billingOrder = $this->billingManager->createMollieOrder(Companies::CID, $orga, $recipient, $action, $lines, $successUrl);
            }

            // Start printing workflow
            $this->printingWorkflow->order($order, $billingOrder);

            // Redirect to checkout/success
            if ($order->isSubrogatedOrder()) {
                return $this->redirect($successUrl);
            }

            return $this->redirect($this->billingManager->getMollieOrder($billingOrder)->getCheckoutUrl());
        }

        return $this->render('console/project/community/printing/order/order.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'orderLines' => $lines,
        ]);
    }

    #[Route('/{uuid}/ordered', name: 'console_community_printing_ordered')]
    public function ordered(PrintingOrder $order)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/community/printing/order/ordered.html.twig', [
            'order' => $order,
        ]);
    }
}
