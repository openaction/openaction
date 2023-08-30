<?php

namespace App\Controller\Console\Organization;

use App\Billing\BillingManager;
use App\Controller\AbstractController;
use App\Entity\Billing\Order;
use App\Form\Billing\Model\UpdateBillingDetailsData;
use App\Form\Billing\UpdateBillingDetailsType;
use App\Platform\Permissions;
use App\Repository\Billing\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/billing')]
class BillingController extends AbstractController
{
    #[Route('/details', name: 'console_organization_billing_details')]
    public function details(EntityManagerInterface $em, BillingManager $billingManager, Request $request)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_BILLING_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        $data = UpdateBillingDetailsData::createFromOrganization($orga);

        $form = $this->createForm(UpdateBillingDetailsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save in database
            $orga->applyBillingDetailsUpdate($data);

            $em->persist($orga);
            $em->flush();

            // Persist on Mollie
            $billingManager->persistMollieCustomer($orga);

            $this->addFlash('success', 'billing.updated_success');

            return $this->redirectToRoute('console_organization_billing_details', [
                'organizationUuid' => $orga->getUuid(),
            ]);
        }

        return $this->render('console/organization/billing/details.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/history', name: 'console_organization_billing_history')]
    public function history(OrderRepository $repository)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_BILLING_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        return $this->render('console/organization/billing/history.html.twig', [
            'invoices' => $repository->findInvoicesHistory($orga),
        ]);
    }

    #[Route('/{uuid}/processed', name: 'console_organization_billing_order_processed', methods: ['GET'])]
    public function processed(Order $order)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_BILLING_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($order);

        return $this->render('console/organization/billing/orderProcessed.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{uuid}/pay', name: 'console_organization_billing_order_pay', methods: ['GET'])]
    public function pay(BillingManager $billingManager, Order $order)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($order);

        if (!$order = $billingManager->getMollieOrder($order)) {
            throw $this->createNotFoundException();
        }

        return $this->redirect($order->getCheckoutUrl());
    }

    #[Route('/{uuid}/download', name: 'console_organization_billing_download', methods: ['GET'])]
    public function download(Order $order, FilesystemReader $cdnStorage)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_BILLING_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($order);

        if (!$invoice = $order->getInvoicePdf()) {
            throw $this->createNotFoundException();
        }

        $path = $invoice->getPathname();
        $response = new StreamedResponse(
            static function () use ($cdnStorage, $path) {
                stream_copy_to_stream($cdnStorage->readStream($path), fopen('php://output', 'wb'));
            }
        );

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'citipo-invoice-'.$order->getInvoiceNumber().'-'.$order->getCreatedAt()->format('Y-m-d').'.pdf'
        );

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
