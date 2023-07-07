<?php

namespace App\Bridge\Sendgrid;

use SendGrid\Mail\Mail;
use SendGrid\Response;

class MockSendgrid implements SendgridInterface
{
    /**
     * @var Mail[]
     */
    public array $mails = [];

    public array $domains = [];
    public array $brandedLinks = [];
    public array $domainValidationRequests = [];
    public array $brandedLinkValidationRequests = [];

    public function sendMessage(Mail $mail): Response
    {
        $this->mails[] = $mail;

        return new Response();
    }

    public function verifySignature(string $content, string $signature, string $timestamp): bool
    {
        return true;
    }

    public function findDomainsByName(string $domain): array
    {
        $results = [];
        foreach ($this->domains as $d) {
            if ($domain === $d['domain']) {
                $results[] = $d;
            }
        }

        return $results;
    }

    public function removeDomain(int $id): void
    {
        foreach ($this->domains as $k => $domain) {
            if ($id === $domain['id']) {
                unset($this->domains[$k]);
            }
        }
    }

    public function createDomain(string $domain): int
    {
        $this->domains[$domain] = $this->createMockDomainConfig($domain);

        return $this->domains[$domain]['id'];
    }

    public function findBrandedLinksByName(string $domain): array
    {
        $results = [];
        foreach ($this->brandedLinks as $link) {
            if ($domain === $link['domain']) {
                $results[] = $link;
            }
        }

        return $results;
    }

    public function removeBrandedLink(int $id): void
    {
        foreach ($this->brandedLinks as $k => $link) {
            if ($id === $link['id']) {
                unset($this->brandedLinks[$k]);
            }
        }
    }

    public function createBrandedLink(string $domain): int
    {
        $this->brandedLinks[$domain] = $this->createMockBrandedLinkConfig($domain);

        return $this->brandedLinks[$domain]['id'];
    }

    public function getDomainConfig(int $domainAuthId): array
    {
        foreach ($this->domains as $domain) {
            if ($domainAuthId === $domain['id']) {
                return $domain;
            }
        }

        throw new \InvalidArgumentException('Sendgrid domain with ID '.$domainAuthId.' not found');
    }

    public function getBrandedLinkConfig(int $brandedLinkId): array
    {
        foreach ($this->brandedLinks as $link) {
            if ($brandedLinkId === $link['id']) {
                return $link;
            }
        }

        throw new \InvalidArgumentException('Sendgrid branded link with ID '.$brandedLinkId.' not found');
    }

    public function requestDomainConfigValidation(int $domainAuthId): void
    {
        $this->domainValidationRequests[$domainAuthId] = true;
    }

    public function requestBrandedLinkConfigValidation(int $brandedLinkId): void
    {
        $this->brandedLinkValidationRequests[$brandedLinkId] = true;
    }

    private function createMockDomainConfig(string $domain): array
    {
        return [
            'id' => 1,
            'domain' => $domain,
            'subdomain' => 'mail',
            'ips' => [],
            'custom_spf' => false,
            'default' => false,
            'legacy' => false,
            'automatic_security' => true,
            'valid' => false,
            'dns' => [
                'mail_cname' => [
                    'valid' => false,
                    'type' => 'cname',
                    'host' => 'mail.'.$domain,
                    'data' => 'sendgrid.net',
                ],
                'dkim1' => [
                    'valid' => false,
                    'type' => 'cname',
                    'host' => 's1._domainkey.'.$domain,
                    'data' => 's1.domainkey.u17150189.wl190.sendgrid.net',
                ],
                'dkim2' => [
                    'valid' => false,
                    'type' => 'cname',
                    'host' => 's2._domainkey.'.$domain,
                    'data' => 's2.domainkey.u17150189.wl190.sendgrid.net',
                ],
            ],
        ];
    }

    private function createMockBrandedLinkConfig(string $domain): array
    {
        return [
            'id' => 2,
            'domain' => $domain,
            'valid' => false,
            'default' => false,
            'legacy' => false,
            'dns' => [
                'domain_cname' => [
                    'valid' => false,
                    'type' => 'cname',
                    'host' => 'mail.'.$domain,
                    'data' => 'sendgrid.net',
                ],
                'owner_cname' => [
                    'valid' => false,
                    'type' => 'cname',
                    'host' => '17150189.'.$domain,
                    'data' => 'sendgrid.net',
                ],
            ],
        ];
    }
}
