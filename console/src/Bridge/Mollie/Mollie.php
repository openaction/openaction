<?php

namespace App\Bridge\Mollie;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Order;

class Mollie implements MollieInterface
{
    private string $apiKey;
    private ?MollieApiClient $client = null;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function createCustomer(string $name, string $email, array $metadata): Customer
    {
        return $this->getClient()->customers->create([
            'name' => $name,
            'email' => $email,
            'locale' => 'fr_FR',
            'metadata' => $metadata,
        ]);
    }

    public function updateCustomer(string $id, string $name, string $email, array $metadata): Customer
    {
        return $this->getClient()->customers->update($id, [
            'name' => $name,
            'email' => $email,
            'metadata' => $metadata,
        ]);
    }

    public function createOrder(array $data): Order
    {
        return $this->getClient()->orders->create($data);
    }

    public function getOrder(string $id): ?Order
    {
        try {
            return $this->getClient()->orders->get($id);
        } catch (ApiException) {
            return null;
        }
    }

    private function getClient(): MollieApiClient
    {
        if (!$this->client) {
            $this->client = new MollieApiClient();
            $this->client->setApiKey($this->apiKey);
        }

        return $this->client;
    }
}
