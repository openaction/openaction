<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Billing\BillingManager;
use App\Billing\Invoice\GenerateQuotePdfMessage;
use App\Community\Printing\PrintingPriceCalculator;
use App\Controller\AbstractController;
use App\Entity\Billing\Model\OrderRecipient;
use App\Form\Community\Printing\Model\QuoteData;
use App\Form\Community\Printing\QuoteType;
use App\Platform\Companies;
use App\Platform\Permissions;
use App\Platform\Products;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/printing/quote')]
class QuoteController extends AbstractController
{
    private PrintingPriceCalculator $priceCalculator;
    private BillingManager $billingManager;
    private MessageBusInterface $bus;

    public function __construct(PrintingPriceCalculator $c, BillingManager $m, MessageBusInterface $bus)
    {
        $this->priceCalculator = $c;
        $this->billingManager = $m;
        $this->bus = $bus;
    }

    #[Route('', name: 'console_community_printing_quote', methods: ['GET'])]
    public function type()
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/community/printing/quote/type.html.twig');
    }

    #[Route('/{type}', requirements: ['type' => 'official|campaign'], name: 'console_community_printing_quote_form', methods: ['GET', 'POST'])]
    public function form(Request $request, string $type)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $data = new QuoteData($orga);

        if ('official' === $type) {
            $data->quantities = [
                Products::PRINT_OFFICIAL_POSTER => 0,
                Products::PRINT_OFFICIAL_BANNER => 0,
                Products::PRINT_OFFICIAL_PLEDGE => 0,
                Products::PRINT_OFFICIAL_BALLOT => 0,
            ];
        } else {
            $data->quantities = [
                Products::PRINT_CAMPAIGN_POSTER => 0,
                Products::PRINT_CAMPAIGN_FLYER => 0,
                Products::PRINT_CAMPAIGN_BOOKLET_4 => 0,
                Products::PRINT_CAMPAIGN_BOOKLET_8 => 0,
                Products::PRINT_CAMPAIGN_LARGE_FLYER => 0,
                Products::PRINT_CAMPAIGN_LETTER => 0,
                Products::PRINT_CAMPAIGN_DOOR => 0,
                Products::PRINT_CAMPAIGN_CARD => 0,
            ];
        }

        $form = $this->createForm(QuoteType::class, $data, ['products' => array_keys($data->quantities)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist billing details in organization
            $orga->applyBillingDetailsUpdate($data->createUpdateBillingDetailsData($orga));
            $this->billingManager->persistMollieCustomer($orga);

            // Create lines
            $printingOrder = $data->createPrintingOrder($this->getProject());
            $lines = $this->priceCalculator->createOrderLines($printingOrder);

            // Create quote
            $quote = $this->billingManager->createQuote(Companies::CID, $orga, OrderRecipient::fromUser($this->getUser()), $lines);

            // Generate it and send it by email
            $this->bus->dispatch(new GenerateQuotePdfMessage($quote->getId()));

            // Redirect to platform
            $this->addFlash('success', 'billing.quote_success');

            return $this->redirectToRoute('console_community_printing', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/quote/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
