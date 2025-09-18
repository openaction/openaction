<?php

namespace App\Tests\Controller;

use App\Client\Citipo;
use App\Client\CitipoInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class PetitionControllerTest extends WebTestCase
{
    public function testViewShowsLocalizedTitleAndDescription(): void
    {
        $client = self::createClient();

        $container = static::getContainer();

        // Prepare a fake Console API using MockHttpClient and keep the real client class.
        $theme = [
            'head.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/head.html.twig'),
            'header.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/header.html.twig'),
            'footer.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/footer.html.twig'),
            'layout.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/layout.html.twig'),
        ];

        $projectPayload = [
            '_resource' => 'project',
            'id' => 'proj1',
            'name' => 'Test Project',
            'locale' => 'fr',
            'metaTitle' => 'Test',
            'metaDescription' => 'Test Project',
            'fontTitle' => 'Inter',
            'fontText' => 'Inter',
            'tools' => ['website_petitions'],
            'access' => ['username' => null, 'password' => null],
            'links' => [
                'stylesheet' => '/css/theme.css',
                'javascript' => '/js/bundle.js',
                'analytics' => '/stats',
                'javascript_custom' => '/js/custom.js',
            ],
            'animateLinks' => false,
            'animateElements' => false,
            'captchaSiteKey' => null,
            'captchaSecretKey' => null,
            'icon' => null,
            'favicon' => null,
            'primary' => '000000',
            'terminology' => ['posts' => 'Posts'],
            'sharer' => null,
            'theme' => $theme,
            'theme_assets' => [],
            'project_assets' => [],
            'redirections' => [],
            'importedRedirections' => [],
            'pages' => [],
            'posts' => [],
            'home' => [],
        ];

        $petitionPayload = [
            '_resource' => 'website_petition_full',
            'slug' => 'stop-foo',
            'localized' => [
                'fr' => ['title' => 'Titre FR', 'description' => 'Description FR'],
                'en' => ['title' => 'Title EN', 'description' => 'Description EN'],
            ],
        ];

        $mock = new MockHttpClient(function (string $method, string $url) use ($projectPayload, $petitionPayload) {
            $path = parse_url($url, PHP_URL_PATH) ?: '';
            if ('GET' === $method && '/api/project' === $path) {
                return new MockResponse(json_encode($projectPayload, JSON_THROW_ON_ERROR), ['response_headers' => ['content-type' => 'application/json']]);
            }
            if ('GET' === $method && preg_match('~^/api/website/petitions/[^/]+$~', $path)) {
                return new MockResponse(json_encode($petitionPayload, JSON_THROW_ON_ERROR), ['response_headers' => ['content-type' => 'application/json']]);
            }

            return new MockResponse('', ['http_code' => 404]);
        });

        $citipo = new Citipo($mock, $container->get('request_stack'));
        $container->set(CitipoInterface::class, $citipo);

        $client->request('GET', '/pe/stop-foo/fr');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Titre FR', $client->getResponse()->getContent());
        $this->assertStringContainsString('Description FR', $client->getResponse()->getContent());
    }
}
