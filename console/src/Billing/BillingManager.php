<?php

namespace App\Billing;

use App\Billing\Model\OrderLine;
use App\Bridge\Mollie\MollieInterface;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Entity\Organization;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Mollie\Api\Resources\Order as MollieOrder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BillingManager
{
    private MollieInterface $mollie;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;

    /**
     * @var MollieOrder[]
     */
    private array $createdMollieOrders = [];

    public function __construct(MollieInterface $m, EntityManagerInterface $em, TranslatorInterface $t, UrlGeneratorInterface $r)
    {
        $this->mollie = $m;
        $this->em = $em;
        $this->translator = $t;
        $this->router = $r;
    }

    public function persistMollieCustomer(Organization $orga)
    {
        $metadata = [
            'uuid' => $orga->getUuid()->toRfc4122(),
            'streetLine1' => $orga->getBillingAddressStreetLine1(),
            'streetLine2' => $orga->getBillingAddressStreetLine2(),
            'postalCode' => $orga->getBillingAddressPostalCode(),
            'city' => $orga->getBillingAddressCity(),
            'country' => $orga->getBillingAddressCountry(),
        ];

        // If a customer already exists, update it
        if ($id = $orga->getMollieCustomerId()) {
            $this->mollie->updateCustomer($id, $orga->getBillingName(), $orga->getBillingEmail(), $metadata);

            return;
        }

        // Otherwise, create it
        $customer = $this->mollie->createCustomer($orga->getBillingName(), $orga->getBillingEmail(), $metadata);
        $orga->setMollieCustomerId($customer->id);

        $this->em->persist($orga);
        $this->em->flush();
    }

    public function getMollieOrder(Order $order): ?MollieOrder
    {
        if ($mollieOrder = $this->createdMollieOrders[$order->getUuid()->toRfc4122()] ?? null) {
            return $mollieOrder;
        }

        return $this->mollie->getOrder($order->getMollieId());
    }

    public function createProductLine(string $product, int $quantity, float $unitPrice, float $vatRate = 20): OrderLine
    {
        return new OrderLine(
            OrderLine::TYPE_PRODUCT,
            $this->translator->trans('products.'.$product, [], 'global'),
            $this->translator->trans('products_help.'.$product, [], 'global'),
            $quantity,
            $unitPrice,
            $vatRate,
        );
    }

    public function createShippingLine(string $product, float $price, int $weight = 0, float $vatRate = 20.0): OrderLine
    {
        return new OrderLine(
            OrderLine::TYPE_SHIPPING_FEE,
            $this->translator->trans('products.'.$product, [], 'global'),
            $this->translator->trans('products_help.'.$product, ['%weight%' => $weight], 'global'),
            1,
            $price,
            $vatRate,
        );
    }

    /**
     * @param OrderLine[] $lines
     */
    public function createMollieOrder(string $company, Organization $orga, OrderRecipient $recipient, OrderAction $action, array $lines, string $redirectUrl = null): Order
    {
        if (!$orga->getMollieCustomerId()) {
            throw new \InvalidArgumentException('Missing customer ID for organization '.$orga->getName());
        }

        // Compute order details
        $amount = 0;
        $linesData = [];

        foreach ($lines as $line) {
            $amount += round($line->getTotalAmountIncludingTaxes(), 2);

            $linesData[] = [
                'type' => 'digital',
                'name' => $line->getName(),
                'quantity' => $line->getQuantity(),
                'unitPrice' => ['currency' => 'EUR', 'value' => $this->formatAmount($line->getUnitPriceIncludingTaxes())],
                'totalAmount' => ['currency' => 'EUR', 'value' => $this->formatAmount($line->getTotalAmountIncludingTaxes())],
                'vatRate' => $this->formatAmount($line->getVatRate()),
                'vatAmount' => ['currency' => 'EUR', 'value' => $this->formatAmount($line->getTotalVatAmount())],
            ];
        }

        // Create in Mollie
        $uuid = Uid::random();

        // Only provide webhook if the URL isn't localhost (to ease local development)
        $webhookUrl = $this->router->generate('webhook_mollie_event', ['uuid' => $uuid], UrlGeneratorInterface::ABSOLUTE_URL);
        if (str_starts_with($webhookUrl, 'http://localhost')) {
            $webhookUrl = null;
        }

        $mollieOrder = $this->mollie->createOrder([
            'orderNumber' => $uuid,
            'amount' => ['currency' => 'EUR', 'value' => $this->formatAmount($amount)],
            'lines' => $linesData,
            'billingAddress' => [
                'organizationName' => $orga->getBillingName(),
                'givenName' => $recipient->getFirstName(),
                'familyName' => $recipient->getLastName(),
                'email' => $recipient->getEmail(),
                'streetAndNumber' => $orga->getBillingAddressStreetLine1(),
                'streetAdditional' => $orga->getBillingAddressStreetLine2() ?: '',
                'postalCode' => $orga->getBillingAddressPostalCode(),
                'city' => $orga->getBillingAddressCity(),
                'country' => $orga->getBillingAddressCountry(),
            ],
            'redirectUrl' => $redirectUrl ?: $this->router->generate(
                'console_organization_billing_order_processed',
                ['organizationUuid' => $orga->getUuid(), 'uuid' => $uuid],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
            'webhookUrl' => $webhookUrl,
            'locale' => 'fr' === $recipient->getLocale() ? 'fr_FR' : 'en_US',
            'method' => 'banktransfer',
            'payment' => [
                'customerId' => $orga->getMollieCustomerId(),
            ],
        ]);

        // Store reference to avoid requesting is right after creation
        $this->createdMollieOrders[$uuid->toRfc4122()] = $mollieOrder;

        // Persist in database
        $order = new Order($uuid, $company, $orga, $mollieOrder->id, $action, $recipient, $lines, round($amount, 2) * 100);

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param OrderLine[] $lines
     */
    public function createManualOrder(string $company, Organization $orga, OrderRecipient $recipient, OrderAction $action, array $lines): Order
    {
        $amount = 0;
        foreach ($lines as $line) {
            $amount += $line->getTotalAmountIncludingTaxes();
        }

        $order = new Order(Uid::random(), $company, $orga, null, $action, $recipient, $lines, $amount * 100);

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    public function createQuote(string $company, Organization $orga, OrderRecipient $recipient, array $lines): Quote
    {
        $amount = 0;
        foreach ($lines as $line) {
            $amount += $line->getTotalAmountIncludingTaxes();
        }

        $nextNumber = $this->em->getRepository(Quote::class)->findNextQuoteNumber();
        $quote = new Quote($company, $orga, $recipient, $lines, (int) ($amount * 100), $nextNumber);

        $this->em->persist($quote);
        $this->em->flush();

        return $quote;
    }

    public function markOrderToPay(Order $order): void
    {
        $order->markToPay($this->em->getRepository(Order::class)->findNextInvoiceNumber());

        $this->em->persist($order);
        $this->em->flush();
    }

    public function markManualOrderPaid(Order $order): void
    {
        $order->markPaid($this->em->getRepository(Order::class)->findNextInvoiceNumber(), new \DateTime());

        $this->em->persist($order);
        $this->em->flush();
    }

    private function formatAmount(float $amount): string
    {
        return number_format(round($amount, 2), 2, '.', '');
    }
}
