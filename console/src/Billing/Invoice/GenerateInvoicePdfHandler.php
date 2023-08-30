<?php

namespace App\Billing\Invoice;

use App\Billing\Invoice\Generator\PdfGenerator;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Mailer\PlatformMailer;
use App\Repository\Billing\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GenerateInvoicePdfHandler implements MessageHandlerInterface
{
    private OrderRepository $orderRepository;
    private PdfGenerator $pdfGenerator;
    private CdnUploader $cdnUploader;
    private EntityManagerInterface $em;
    private PlatformMailer $mailer;

    public function __construct(OrderRepository $or, PdfGenerator $g, CdnUploader $u, EntityManagerInterface $em, PlatformMailer $m)
    {
        $this->orderRepository = $or;
        $this->pdfGenerator = $g;
        $this->cdnUploader = $u;
        $this->em = $em;
        $this->mailer = $m;
    }

    public function __invoke(GenerateInvoicePdfMessage $message)
    {
        if (!$order = $this->orderRepository->find($message->getOrderId())) {
            return true;
        }

        if (!$order->getPaidAt() || !$order->getInvoiceNumber()) {
            return true;
        }

        // Generate the invoice
        $invoiceFilename = sys_get_temp_dir().'/citipo-invoice-'.date('Y-m-d').'-'.$order->getUuid().'.pdf';
        file_put_contents($invoiceFilename, $this->pdfGenerator->generateInvoice($order));

        try {
            // Upload it and save it in the order
            $order->setInvoicePdf($this->cdnUploader->upload(
                CdnUploadRequest::createOrganizationPrivateFileRequest(new File($invoiceFilename))
            ));

            // Persist
            $this->em->persist($order);
            $this->em->flush();

            // Send the invoice by email
            if ($order->getOrganization()->getBillingEmail()) {
                $this->mailer->sendNotificationNewInvoice($order, $invoiceFilename);
            }
        } finally {
            @unlink($invoiceFilename);
        }

        $order->markInvoiceSent();
        $this->em->persist($order);
        $this->em->flush();

        return true;
    }
}
