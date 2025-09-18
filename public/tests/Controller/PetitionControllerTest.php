<?php

namespace App\Tests\Controller;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use App\Client\PassKey\TokenResolver;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PetitionControllerTest extends WebTestCase
{
    public function testViewShowsLocalizedTitleAndDescription(): void
    {
        $client = self::createClient();

        // Stub token resolver to always resolve a token for localhost
        $tokenResolver = new class extends TokenResolver {
            public function __construct()
            {
            }

            public function resolveProjectToken(string $domain): ?string
            {
                return 'TEST_TOKEN';
            }
        };
        static::getContainer()->set(TokenResolver::class, $tokenResolver);

        // Mock Citipo client
        $citipo = $this->createMock(CitipoInterface::class);

        $project = new ApiResource();
        $project->name = 'Test Project';
        $project->locale = 'fr';
        $project->tools = ['website_petitions'];
        $project->access = ['username' => null, 'password' => null];
        $project->redirections = [];
        $project->importedRedirections = [];
        $project->animateLinks = false;
        $project->captchaSiteKey = null;
        $project->captchaSecretKey = null;
        $project->metaTitle = 'Test';
        $project->fontTitle = 'Inter';
        $project->fontText = 'Inter';
        $project->links = [
            'stylesheet' => '/css/theme.css',
            'javascript' => '/js/bundle.js',
            'analytics' => '/stats',
            'javascript_custom' => '/js/custom.js',
        ];
        $project->id = 'proj1';
        $project->animateElements = false;
        $project->icon = null;
        $project->favicon = null;
        $project->primary = '000000';
        $project->terminology = ['posts' => 'Posts'];
        $project->sharer = null;
        $project->theme = [
            'head.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/head.html.twig'),
            'header.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/header.html.twig'),
            'footer.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/footer.html.twig'),
            'layout.html.twig' => file_get_contents(__DIR__.'/../Fixtures/theme/layout.html.twig'),
        ];
        $project->theme_assets = [];
        $project->project_assets = [];
        $citipo->method('getProject')->willReturn($project);

        $petition = new ApiResource();
        $petition->slug = 'stop-foo';
        $petition->localized = [
            'fr' => ['title' => 'Titre FR', 'description' => 'Description FR'],
            'en' => ['title' => 'Title EN', 'description' => 'Description EN'],
        ];
        $citipo->method('getPetition')->willReturn($petition);

        static::getContainer()->set(CitipoInterface::class, $citipo);

        $client->request('GET', '/pe/stop-foo/fr');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Titre FR', $client->getResponse()->getContent());
        $this->assertStringContainsString('Description FR', $client->getResponse()->getContent());
    }
}
