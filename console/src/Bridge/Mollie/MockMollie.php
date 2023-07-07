<?php

namespace App\Bridge\Mollie;

use App\Util\Uid;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderLine;
use Symfony\Component\Uid\Uuid;

class MockMollie implements MollieInterface
{
    public array $customers = [];
    public array $orders = [];

    public function __construct()
    {
        // Customer
        $this->customers['cst_DKnSArGRCm'] = $this->createMockCustomer(
            'cst_DKnSArGRCm',
            'Acme Inc',
            'billing@example.com',
            [
                'uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91',
                'streetLine1' => 'Street line 1',
                'streetLine2' => 'Street line 2',
                'postalCode' => 'Postal code',
                'city' => 'City',
                'country' => 'FR',
            ]
        );

        // Draft
        $this->orders['ord_35bc37bc'] = $this->createMockOrder('ord_35bc37bc', [
            'orderNumber' => Uid::toBase62(Uuid::fromString('35bc37bc-8e8a-4d37-b5c5-9ec5a62eee28')),
            'amount' => ['currency' => 'EUR', 'value' => '18.00'],
            'status' => 'paid',
        ]);

        $this->orders['ord_7698f242'] = $this->createMockOrder('ord_7698f242', [
            'orderNumber' => Uid::toBase62(Uuid::fromString('7698f242-0a05-496c-b542-34236e9de12c')),
            'amount' => ['currency' => 'EUR', 'value' => '18.00'],
            'status' => 'open',
        ]);

        // Pending
        $this->orders['ord_1c183332'] = $this->createMockOrder('ord_1c183332', [
            'orderNumber' => Uid::toBase62(Uuid::fromString('1c183332-2811-43fe-89e7-795f2817d04b')),
            'amount' => ['currency' => 'EUR', 'value' => '18.00'],
            'status' => 'open',
            'billingAddress' => [
                'organizationName' => 'CPGT SAS',
                'givenName' => 'Titouan',
                'familyName' => 'Galopin',
                'email' => 'billing@citipo.com',
                'streetAndNumber' => '49 Rue de Ponthieu',
                'streetAdditional' => 'Etage 1',
                'postalCode' => '75008',
                'city' => 'Paris',
                'country' => 'FR',
            ],
            'lines' => [
                [
                    'type' => 'digital',
                    'name' => '100 email credits',
                    'quantity' => '10',
                    'unitPrice' => ['currency' => 'EUR', 'value' => '0.30'],
                    'totalAmount' => ['currency' => 'EUR', 'value' => '3.60'],
                    'vatRate' => '20.00',
                    'vatAmount' => ['currency' => 'EUR', 'value' => '0.60'],
                ],
            ],
            'locale' => 'fr_FR',
            'method' => 'banktransfer',
            'payment' => [
                'customerId' => 'cst_DKnSArGRCm',
            ],
        ]);

        $this->orders['ord_38889b3e'] = $this->createMockOrder('ord_38889b3e', [
            'orderNumber' => Uid::toBase62(Uuid::fromString('38889b3e-de86-4d58-884b-e0190722148f')),
            'amount' => ['currency' => 'EUR', 'value' => '18.00'],
            'status' => 'open',
            'billingAddress' => [
                'organizationName' => 'CPGT SAS',
                'givenName' => 'Titouan',
                'familyName' => 'Galopin',
                'email' => 'billing@citipo.com',
                'streetAndNumber' => '49 Rue de Ponthieu',
                'streetAdditional' => 'Etage 1',
                'postalCode' => '75008',
                'city' => 'Paris',
                'country' => 'FR',
            ],
            'lines' => [
                [
                    'type' => 'digital',
                    'name' => '100 email credits',
                    'quantity' => '10',
                    'unitPrice' => ['currency' => 'EUR', 'value' => '0.30'],
                    'totalAmount' => ['currency' => 'EUR', 'value' => '3.60'],
                    'vatRate' => '20.00',
                    'vatAmount' => ['currency' => 'EUR', 'value' => '0.60'],
                ],
            ],
            'locale' => 'fr_FR',
            'method' => 'banktransfer',
            'payment' => [
                'customerId' => 'cst_DKnSArGRCm',
            ],
        ]);

        // Expired
        $this->orders['ord_bdd21e4d'] = $this->createMockOrder('ord_bdd21e4d', [
            'orderNumber' => Uid::toBase62(Uuid::fromString('bdd21e4d-93f5-4c1c-97fd-7ba730ee4394')),
            'amount' => ['currency' => 'EUR', 'value' => '18.00'],
            'status' => 'expired',
        ]);

        // Paid
        $this->orders['ord_b1e80c11'] = $this->createMockOrder('ord_b1e80c11', [
            'orderNumber' => Uid::toBase62(Uuid::fromString('b1e80c11-ca03-4e11-858a-dc00b05c5527')),
            'amount' => ['currency' => 'EUR', 'value' => '18.00'],
            'status' => 'paid',
            'billingAddress' => [
                'organizationName' => 'CPGT SAS',
                'givenName' => 'Titouan',
                'familyName' => 'Galopin',
                'email' => 'billing@citipo.com',
                'streetAndNumber' => '49 Rue de Ponthieu',
                'streetAdditional' => 'Etage 1',
                'postalCode' => '75008',
                'city' => 'Paris',
                'country' => 'FR',
            ],
            'lines' => [
                [
                    'type' => 'digital',
                    'name' => '100 email credits',
                    'quantity' => '10',
                    'unitPrice' => ['currency' => 'EUR', 'value' => '0.30'],
                    'totalAmount' => ['currency' => 'EUR', 'value' => '3.60'],
                    'vatRate' => '20.00',
                    'vatAmount' => ['currency' => 'EUR', 'value' => '0.60'],
                ],
            ],
            'locale' => 'fr_FR',
            'method' => 'banktransfer',
            'payment' => [
                'customerId' => 'cst_DKnSArGRCm',
            ],
        ]);
    }

    public function createCustomer(string $name, string $email, array $metadata): Customer
    {
        $id = 'cst_'.substr(md5($name), 0, 8);

        return $this->customers[$id] = $this->createMockCustomer($id, $name, $email, $metadata);
    }

    public function updateCustomer(string $id, string $name, string $email, array $metadata): Customer
    {
        return $this->customers[$id] = $this->createMockCustomer($id, $name, $email, $metadata);
    }

    public function createOrder(array $data): Order
    {
        $id = 'ord_'.substr(md5($data['orderNumber']), 0, 8);

        return $this->orders[$id] = $this->createMockOrder($id, $data);
    }

    public function getOrder(string $id): ?Order
    {
        return $this->orders[$id] ?? null;
    }

    private function createMockCustomer(string $id, string $name, string $email, array $metadata): Customer
    {
        /** @var Customer $customer */
        $customer = (new \ReflectionClass(Customer::class))->newInstanceWithoutConstructor();
        $customer->id = $id;
        $customer->name = $name;
        $customer->email = $email;
        $customer->metadata = (object) $metadata;

        return $customer;
    }

    private function createMockOrder(string $id, array $data): Order
    {
        /** @var Order $order */
        $order = (new \ReflectionClass(Order::class))->newInstanceWithoutConstructor();
        $order->id = $id;
        $order->status = $data['status'] ?? 'created';
        $order->orderNumber = $data['orderNumber'];
        $order->amount = (object) $data['amount'];
        $order->billingAddress = new \stdClass();
        $order->billingAddress->organizationName = $data['billingAddress']['organizationName'] ?? null;
        $order->billingAddress->streetAndNumber = $data['billingAddress']['streetAndNumber'] ?? null;
        $order->billingAddress->streetAdditional = $data['billingAddress']['streetAdditional'] ?? null;
        $order->billingAddress->city = $data['billingAddress']['city'] ?? null;
        $order->billingAddress->region = $data['billingAddress']['region'] ?? null;
        $order->billingAddress->postalCode = $data['billingAddress']['postalCode'] ?? null;
        $order->billingAddress->country = $data['billingAddress']['country'] ?? null;
        $order->billingAddress->title = $data['billingAddress']['title'] ?? null;
        $order->billingAddress->givenName = $data['billingAddress']['givenName'] ?? null;
        $order->billingAddress->familyName = $data['billingAddress']['familyName'] ?? null;
        $order->billingAddress->email = $data['billingAddress']['email'] ?? null;
        $order->billingAddress->phone = $data['billingAddress']['phone'] ?? null;
        $order->redirectUrl = $data['redirectUrl'] ?? '';
        $order->webhookUrl = $data['webhookUrl'] ?? '';
        $order->method = $data['method'] ?? 'banktransfer';
        $order->_links = (object) [
            'checkout' => (object) ['href' => 'https://mollie.com/checkout'],
        ];

        $order->lines = [];
        foreach ($data['lines'] ?? [] as $lineData) {
            /** @var OrderLine $line */
            $line = (new \ReflectionClass(OrderLine::class))->newInstanceWithoutConstructor();
            $line->type = $lineData['type'] ?? 'digital';
            $line->name = $lineData['name'];
            $line->quantity = $lineData['quantity'];
            $line->unitPrice = (object) $lineData['unitPrice'];
            $line->totalAmount = (object) $lineData['totalAmount'];
            $line->vatRate = $lineData['vatRate'];
            $line->vatAmount = (object) $lineData['vatAmount'];

            $order->lines[] = $line;
        }

        return $order;
    }
}
