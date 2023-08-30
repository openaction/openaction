<?php

namespace App\Bridge\Mollie;

use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Order;

interface MollieInterface
{
    public function createCustomer(string $name, string $email, array $metadata): Customer;

    public function updateCustomer(string $id, string $name, string $email, array $metadata): Customer;

    public function createOrder(array $data): Order;

    public function getOrder(string $id): ?Order;
}
