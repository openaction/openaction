<?php

namespace App\Bridge\Sentry;

use App\Kernel;
use Jean85\PrettyVersions;
use Nyholm\Psr7\Factory\Psr17Factory;
use Sentry\Client;
use Sentry\ClientBuilder;
use Sentry\HttpClient\HttpClientFactory;
use Sentry\Integration\RequestIntegration;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Sentry\Transport\DefaultTransportFactory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttplugClient;

class SentryHubFactory
{
    public function create(?string $dsn, string $projectRoot, string $cacheDir): HubInterface
    {
        $clientBuilder = ClientBuilder::create([
            'dsn' => $dsn ?: null,
            'in_app_include' => [$projectRoot],
            'in_app_exclude' => [$cacheDir, $projectRoot.'/vendor'],
            'prefixes' => [$projectRoot],
            'default_integrations' => false,
            'send_attempts' => 1,
            'tags' => [
                'php_uname' => \PHP_OS,
                'php_sapi_name' => \PHP_SAPI,
                'php_version' => \PHP_VERSION,
                'framework' => 'symfony',
                'symfony_version' => Kernel::VERSION,
            ],
        ]);

        $client = HttpClient::create(['timeout' => 2]);
        $psr17Factory = new Psr17Factory();
        $httpClient = new HttplugClient($client, $psr17Factory, $psr17Factory);

        $httpClientFactory = new HttpClientFactory(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $httpClient,
            Client::SDK_IDENTIFIER,
            PrettyVersions::getVersion('sentry/sentry')->getPrettyVersion()
        );

        $clientBuilder->setTransportFactory(new DefaultTransportFactory($psr17Factory, $psr17Factory, $httpClientFactory));

        // Enable Sentry RequestIntegration
        $options = $clientBuilder->getOptions();
        $options->setIntegrations([new RequestIntegration()]);

        $client = $clientBuilder->getClient();

        // A global HubInterface must be set otherwise some feature provided by the SDK does not work as they rely on this global state
        return SentrySdk::setCurrentHub(new Hub($client));
    }
}
