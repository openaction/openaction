<?php

namespace App\Billing\Invoice;

use App\Billing\Invoice\Generator\PdfGenerator;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Mailer\PlatformMailer;
use App\Repository\Billing\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GenerateQuotePdfHandler implements MessageHandlerInterface
{
    private QuoteRepository $repository;
    private PdfGenerator $pdfGenerator;
    private CdnUploader $cdnUploader;
    private EntityManagerInterface $em;
    private PlatformMailer $mailer;

    public function __construct(QuoteRepository $qr, PdfGenerator $g, CdnUploader $u, EntityManagerInterface $em, PlatformMailer $m)
    {
        $this->repository = $qr;
        $this->pdfGenerator = $g;
        $this->cdnUploader = $u;
        $this->em = $em;
        $this->mailer = $m;
    }

    public function __invoke(GenerateQuotePdfMessage $message)
    {
        // Quote not found or already generated
        if (!($quote = $this->repository->find($message->getQuoteId())) || $quote->getPdf()) {
            return true;
        }

        // Generate the invoice
        $quoteFilename = sys_get_temp_dir().'/citipo-quote-'.date('Y-m-d').'-'.$quote->getUuid().'.pdf';
        file_put_contents($quoteFilename, $this->pdfGenerator->generateQuote($quote));

        try {
            // Upload it and save it in the order
            $quote->setPdf($this->cdnUploader->upload(
                CdnUploadRequest::createOrganizationPrivateFileRequest(new File($quoteFilename))
            ));

            // Persist
            $this->em->persist($quote);
            $this->em->flush();

            // Send the invoice by email
            if ($quote->getOrganization()->getBillingEmail()) {
                $this->mailer->sendNotificationNewQuote($quote, $quoteFilename);
            }
        } finally {
            @unlink($quoteFilename);
        }

        $quote->markPdfSent();
        $this->em->persist($quote);
        $this->em->flush();

        return true;
    }
}
