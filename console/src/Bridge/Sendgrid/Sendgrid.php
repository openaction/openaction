<?php

namespace App\Bridge\Sendgrid;

use App\Util\Json;
use Psr\Log\LoggerInterface;
use SendGrid\Client;
use SendGrid\EventWebhook\EventWebhook;
use SendGrid\Mail\Mail;
use SendGrid\Response;

class Sendgrid implements SendgridInterface
{
    private \SendGrid $sendgrid;
    private string $verificationKey;
    private LoggerInterface $logger;

    public function __construct(string $apiKey, string $verificationKey, LoggerInterface $logger)
    {
        $this->sendgrid = new \SendGrid($apiKey);
        $this->verificationKey = $verificationKey;
        $this->logger = $logger;
    }

    public function sendMessage(Mail $mail): Response
    {
        $response = $this->sendgrid->send($mail);

        if ($response->statusCode() >= 300) {
            $this->logger->error('Sending Sendgrid email failed.', [
                'from' => $mail->getFrom()->getEmail(),
                'status_code' => $response->statusCode(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Sending Sendgrid email failed.');
        }

        return $response;
    }

    public function verifySignature(string $content, string $signature, string $timestamp): bool
    {
        $ew = new EventWebhook();

        return $ew->verifySignature($ew->convertPublicKeyToECDSA($this->verificationKey), $content, $signature, $timestamp);
    }

    public function findDomainsByName(string $domain): array
    {
        return $this->parseResponse($this->getClient()->whitelabel()->domains()->get(null, ['domain' => $domain]));
    }

    public function removeDomain(int $id): void
    {
        $this->getClient()->whitelabel()->domains()->_($id)->delete();
    }

    public function createDomain(string $domain): int
    {
        $config = $this->parseResponse(
            $this->throwOnError($domain, $this->sendgrid->client->whitelabel()->domains()->post([
                'domain' => $domain,
                'automatic_security' => true,
                'default' => false,
            ]))
        );

        return $config['id'];
    }

    public function findBrandedLinksByName(string $domain): array
    {
        return $this->parseResponse($this->getClient()->whitelabel()->links()->get(null, ['domain' => $domain]));
    }

    public function removeBrandedLink(int $id): void
    {
        $this->getClient()->whitelabel()->links()->_($id)->delete();
    }

    public function createBrandedLink(string $domain): int
    {
        $config = $this->parseResponse(
            $this->throwOnError($domain, $this->sendgrid->client->whitelabel()->links()->post([
                'domain' => $domain,
                'subdomain' => 'mail',
                'default' => false,
            ]))
        );

        return $config['id'];
    }

    public function getDomainConfig(int $domainAuthId): array
    {
        return $this->parseResponse($this->sendgrid->client->whitelabel()->domains()->_($domainAuthId)->get());
    }

    public function getBrandedLinkConfig(int $brandedLinkId): array
    {
        return $this->parseResponse($this->sendgrid->client->whitelabel()->links()->_($brandedLinkId)->get());
    }

    public function requestDomainConfigValidation(int $domainAuthId): void
    {
        $this->sendgrid->client->whitelabel()->domains()->_($domainAuthId)->validate()->post();
    }

    public function requestBrandedLinkConfigValidation(int $brandedLinkId): void
    {
        $this->sendgrid->client->whitelabel()->links()->_($brandedLinkId)->validate()->post();
    }

    private function getClient(): Client
    {
        return $this->sendgrid->client;
    }

    private function throwOnError(string $domain, Response $response): Response
    {
        if ($response->statusCode() < 200 || $response->statusCode() >= 300) {
            $this->logger->error('Sendgrid API error', [
                'domain' => $domain,
                'status' => $response->statusCode(),
                'response' => $response->body(),
            ]);

            throw new \LogicException('Sendgrid API error');
        }

        return $response;
    }

    private function parseResponse(Response $response): array
    {
        return Json::decode($response->body());
    }
}
