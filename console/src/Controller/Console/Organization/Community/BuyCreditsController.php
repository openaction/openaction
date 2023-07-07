<?php

namespace App\Controller\Console\Organization\Community;

use App\Billing\BillingManager;
use App\Controller\AbstractController;
use App\Entity\Billing\Model\OrderAction;
use App\Form\Organization\BuyCreditsType;
use App\Form\Organization\Model\BuyCreditsData;
use App\Platform\Companies;
use App\Platform\Permissions;
use App\Platform\Prices;
use App\Platform\Products;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/community/buy-credits')]
class BuyCreditsController extends AbstractController
{
    #[Route('/emails', name: 'console_organization_community_buy_credits_emails')]
    public function emails(BillingManager $billingManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_BILLING_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        $data = new BuyCreditsData(1000, 50_000, 5000, $orga, $this->getUser());

        $form = $this->createForm(BuyCreditsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist billing details in organization
            $orga->applyBillingDetailsUpdate($data->createUpdateBillingDetailsData());
            $billingManager->persistMollieCustomer($orga);

            // Create order and redirect to checkout
            $recipient = $data->createOrderRecipient($this->getUser()->getLocale());
            $action = OrderAction::addEmailCredits($data->amount);

            $order = $billingManager->createMollieOrder(Companies::CITIPO, $orga, $recipient, $action, [
                $billingManager->createProductLine(Products::CREDIT_EMAIL, $data->amount, Prices::CREDIT_EMAIL),
            ]);

            return $this->redirect($billingManager->getMollieOrder($order)->getCheckoutUrl());
        }

        return $this->render('console/organization/community/buyCredits/emails.html.twig', [
            'form' => $form->createView(),
            'unitPrice' => Prices::CREDIT_EMAIL,
            'defaultQuantity' => $data->amount,
        ]);
    }

    #[Route('/texts', name: 'console_organization_community_buy_credits_texts')]
    public function texts(BillingManager $billingManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_BILLING_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        $data = new BuyCreditsData(10, 10_000, 100, $orga, $this->getUser());

        $form = $this->createForm(BuyCreditsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist billing details in organization
            $orga->applyBillingDetailsUpdate($data->createUpdateBillingDetailsData());
            $billingManager->persistMollieCustomer($orga);

            // Create order and redirect to checkout
            $recipient = $data->createOrderRecipient($this->getUser()->getLocale());
            $action = OrderAction::addTextCredits($data->amount);

            $order = $billingManager->createMollieOrder(Companies::CITIPO, $orga, $recipient, $action, [
                $billingManager->createProductLine(Products::CREDIT_TEXT, $data->amount, Prices::CREDIT_TEXT),
            ]);

            return $this->redirect($billingManager->getMollieOrder($order)->getCheckoutUrl());
        }

        return $this->render('console/organization/community/buyCredits/texts.html.twig', [
            'form' => $form->createView(),
            'unitPrice' => Prices::CREDIT_TEXT,
            'defaultQuantity' => $data->amount,
        ]);
    }
}
