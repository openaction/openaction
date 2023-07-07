<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Community\Printing\PrintingAddressFileImporter;
use App\Controller\AbstractController;
use App\Entity\Community\PrintingOrder;
use App\Form\Community\Printing\Model\PrintingOrderAddressedDeliveryData;
use App\Form\Community\Printing\Model\PrintingOrderAddressFileColumnsData;
use App\Form\Community\Printing\Model\PrintingOrderUnaddressedDeliveryData;
use App\Form\Community\Printing\PrintingOrderAddressedDeliveryType;
use App\Form\Community\Printing\PrintingOrderAddressFileColumnsType;
use App\Form\Community\Printing\PrintingOrderUnaddressedDeliveryType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/printing/{uuid}/delivery')]
class DeliveryController extends AbstractController
{
    private EntityManagerInterface $em;
    private PrintingAddressFileImporter $importer;

    public function __construct(EntityManagerInterface $em, PrintingAddressFileImporter $i)
    {
        $this->em = $em;
        $this->importer = $i;
    }

    #[Route('', name: 'console_community_printing_delivery', methods: ['GET', 'POST'])]
    public function delivery(Request $request, PrintingOrder $order)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($order);

        // Already ordered campaigns can't be edited anymore
        if ($order->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        /*
         * Addressed
         */
        $addressedData = new PrintingOrderAddressedDeliveryData();

        $addressedForm = $this->createForm(PrintingOrderAddressedDeliveryType::class, $addressedData);
        $addressedForm->handleRequest($request);

        if ($addressedForm->isSubmitted() && $addressedForm->isValid()) {
            // Import the file
            $this->importer->prepareImport($order, $addressedData);

            // Redirect to columns matching
            return $this->redirectToRoute('console_community_printing_delivery_columns', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $order->getUuid(),
            ]);
        }

        /*
         * Unaddressed
         */
        $unaddressedData = PrintingOrderUnaddressedDeliveryData::fromCampaign($order);

        $unaddressedForm = $this->createForm(PrintingOrderUnaddressedDeliveryType::class, $unaddressedData, [
            'order' => $order,
        ]);
        $unaddressedForm->handleRequest($request);

        if ($unaddressedForm->isSubmitted() && $unaddressedForm->isValid()) {
            $order->applyUnaddressedDelivery($unaddressedData);

            $this->em->persist($order);
            $this->em->flush();

            $this->addFlash('success', 'printing.updated_success');

            return $this->redirectToRoute('console_community_printing_recipient', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $order->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/finalize/delivery.html.twig', [
            'order' => $order,
            'campaigns' => $order->getCampaigns(),
            'addressedForm' => $addressedForm->createView(),
            'unaddressedForm' => $unaddressedForm->createView(),
        ]);
    }

    #[Route('/columns', name: 'console_community_printing_delivery_columns', methods: ['GET', 'POST'])]
    public function columns(Request $request, PrintingOrder $order)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($order);

        // Already ordered campaigns can't be edited anymore
        if ($order->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        // Unaddressed campaign or invalid file
        if (!$order->isDeliveryAddressed()
            || !$order->getDeliveryAddressFile()
            || !$order->getDeliveryAddressFileFirstLines()) {
            return $this->redirectToRoute('console_community_printing_delivery', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $order->getUuid(),
            ]);
        }

        $data = PrintingOrderAddressFileColumnsData::createFromCampaign($order);

        $form = $this->createForm(PrintingOrderAddressFileColumnsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->importer->import($order, $data);

            return $this->redirectToRoute('console_community_printing_delivery_processing', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $order->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/finalize/addresses/columns.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/processing', name: 'console_community_printing_delivery_processing', methods: ['GET'])]
    public function processing(PrintingOrder $order)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($order);

        // Already ordered campaigns can't be edited anymore
        if ($order->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        // Imported
        if ($order->getDeliveryAddressList()) {
            $this->addFlash('success', 'printing.updated_success');

            return $this->redirectToRoute('console_community_printing_recipient', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $order->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/finalize/addresses/processing.html.twig', [
            'order' => $order,
        ]);
    }
}
