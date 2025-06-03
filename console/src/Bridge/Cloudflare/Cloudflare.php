<?php

namespace App\Bridge\Cloudflare;

use App\Entity\Model\CloudflareDomainConfig;
use App\Util\Json;
use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Configurations\PageRulesActions;
use Cloudflare\API\Configurations\PageRulesTargets;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\EndpointException;
use Cloudflare\API\Endpoints\PageRules;
use Cloudflare\API\Endpoints\SSL;
use Cloudflare\API\Endpoints\Zones;
use Cloudflare\API\Endpoints\ZoneSettings;
use GuzzleHttp\Exception\ClientException;

class Cloudflare implements CloudflareInterface
{
    private ?Adapter $httpAdapter = null;

    public function __construct(
        private readonly string $token,
        private readonly string $organizationId,
        private readonly string $trialZoneId,
        private readonly string $trialCname,
        private readonly string $publicServerIp,
    ) {
    }

    public function isEnabled(): bool
    {
        return !empty($this->token);
    }

    public function createRootDomain(string $domain): CloudflareDomainConfig
    {
        // If domain already exists, fetch it, otherwise create it
        try {
            $zoneId = $this->getZonesClient()->getZoneID($domain);
        } catch (EndpointException) {
            $zoneId = null;
        }

        try {
            if ($zoneId) {
                $zone = $this->getZonesClient()->getZoneById($zoneId);
            } else {
                $zone = $this->getZonesClient()->addZone($domain, false, $this->organizationId);
            }
        } catch (\Throwable $e) {
            $this->handleCloudflareApiError($e);
        }

        return new CloudflareDomainConfig($zone->id, $zone->name, $zone->status, $zone->name_servers);
    }

    public function provisionRootDomain(string $zoneId): CloudflareDomainConfig
    {
        try {
            $zone = $this->getZonesClient()->getZoneById($zoneId)->result;

            $dns = $this->getDNSClient();

            // Remove existing conflicting records
            $hasMXRecords = false;
            foreach ($dns->listRecords($zone->id)->result as $record) {
                // Remove root and www A records
                if ('A' === $record->type && ($zone->name === $record->name || 'www.'.$zone->name === $record->name)) {
                    $dns->deleteRecord($zone->id, $record->id);

                    continue;
                }

                // Remove root and www CNAME records
                if ('CNAME' === $record->type
                    && ($zone->name === $record->name || 'www.'.$zone->name === $record->name || 'ca.'.$zone->name === $record->name)) {
                    $dns->deleteRecord($zone->id, $record->id);

                    continue;
                }

                if ('MX' === $record->type && $zone->name === $record->name) {
                    $hasMXRecords = true;
                }
            }

            // Configure Citipo A and CNAME records
            $dns->addRecord($zone->id, 'A', $zone->name, $this->publicServerIp);
            $dns->addRecord($zone->id, 'CNAME', 'www', $zone->name);
            $dns->addRecord($zone->id, 'CNAME', 'ca', 'analytics.citipo.com');

            // Add MX records if no MX records already exist
            if (!$hasMXRecords) {
                $dns->addRecord($zone->id, 'MX', $zone->name, 'mx1.mail.ovh.net', 0, false, '1');
                $dns->addRecord($zone->id, 'MX', $zone->name, 'mx2.mail.ovh.net', 0, false, '5');
                $dns->addRecord($zone->id, 'MX', $zone->name, 'mx3.mail.ovh.net', 0, false, '100');
                $dns->addRecord($zone->id, 'TXT', $zone->name, 'v=spf1 include:mx.ovh.com -all', 0, false);
            }

            try {
                // Configure www redirect page rule
                $redirectWww = new PageRulesActions();
                $redirectWww->setForwardingURL(301, 'https://'.$zone->name.'/$1');
                $this->getPageRulesClient()->createPageRule($zone->id, new PageRulesTargets('www.'.$zone->name.'/*'), $redirectWww);
            } catch (\Throwable) {
                // Ignore duplicate page rules
            }

            // Configure SSL
            $ssl = $this->getSSLClient();
            $ssl->updateSSLSetting($zone->id, 'flexible');
            $ssl->updateHTTPSRedirectSetting($zone->id, 'on');

            // Disable email obfuscation
            $settings = $this->getZoneSettingsClient();
            $settings->updateEmailObfuscationSetting($zone->id, 'off');
        } catch (\Throwable $e) {
            $this->handleCloudflareApiError($e);
        }

        return new CloudflareDomainConfig($zone->id, $zone->name, $zone->status, $zone->name_servers);
    }

    public function createRootDomainTxt(string $zoneId, string $host, string $content): bool
    {
        try {
            return $this->getDNSClient()->addRecord($zoneId, 'TXT', $host, $content, 0, false);
        } catch (\Throwable $e) {
            $this->handleCloudflareApiError($e);
        }

        return false;
    }

    public function createRootDomainCname(string $zoneId, string $host, string $target, bool $dnsOnly = true): bool
    {
        try {
            // If the record already exists, update
            if ($recordId = $this->getDNSClient()->getRecordID($zoneId, 'CNAME', $host)) {
                $this->getDNSClient()->updateRecordDetails($zoneId, $recordId, [
                    'type' => 'CNAME',
                    'name' => $host,
                    'content' => $target,
                    'proxied' => !$dnsOnly,
                ]);

                return true;
            }

            // Otherwise create
            return $this->getDNSClient()->addRecord($zoneId, 'CNAME', $host, $target, 0, !$dnsOnly);
        } catch (\Throwable $e) {
            $this->handleCloudflareApiError($e);
        }

        return false;
    }

    public function getRootDomainConfig(string $zoneId): ?CloudflareDomainConfig
    {
        try {
            $zone = $this->getZonesClient()->getZoneById($zoneId)->result;
        } catch (\Exception) {
            return null;
        }

        return new CloudflareDomainConfig($zone->id, $zone->name, $zone->status, $zone->name_servers);
    }

    public function hasTrialSubdomain(string $subdomain): bool
    {
        return count($this->getDNSClient()->listRecords($this->trialZoneId, 'CNAME', $subdomain)->result) > 0;
    }

    public function createTrialSubdomain(string $subdomain): bool
    {
        return $this->getDNSClient()->addRecord($this->trialZoneId, 'CNAME', $subdomain, $this->trialCname);
    }

    private function getZonesClient(): Zones
    {
        return new Zones($this->getHttpAdapter());
    }

    private function getZoneSettingsClient(): ZoneSettings
    {
        return new ZoneSettings($this->getHttpAdapter());
    }

    private function getSSLClient(): SSL
    {
        return new SSL($this->getHttpAdapter());
    }

    private function getPageRulesClient(): PageRules
    {
        return new PageRules($this->getHttpAdapter());
    }

    private function getDNSClient(): DNS
    {
        return new DNS($this->getHttpAdapter());
    }

    private function getHttpAdapter(): Adapter
    {
        if (!$this->httpAdapter) {
            $this->httpAdapter = new Guzzle(new APIToken($this->token));
        }

        return $this->httpAdapter;
    }

    private function handleCloudflareApiError(\Throwable $exception, \Throwable $root = null): void
    {
        // Recursive calling to get to the HTTP exception
        if (!$exception instanceof ClientException) {
            if ($exception->getPrevious()) {
                $this->handleCloudflareApiError($exception->getPrevious(), $root);

                return;
            }

            // Reached the last exception without HTTP exception, throw the root one again
            throw $root ?: $exception;
        }

        // Reached a HTTP exception
        $message = (string) $exception->getResponse()->getBody();

        try {
            $data = Json::decode($message);

            if (isset($data['messages'])) {
                $message = Json::encode($data['messages']);
            }
        } catch (\Throwable) {
            // Invalid JSON, keep it as string
        }

        throw new \RuntimeException(message: 'Cloudflare API error: '.$message, previous: $root ?: $exception);
    }
}
