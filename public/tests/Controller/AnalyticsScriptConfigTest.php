<?php

namespace App\Tests\Controller;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use App\Client\PassKey\TokenResolver;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Twig\Environment;

class AnalyticsScriptConfigTest extends WebTestCase
{
    public function testStatsApiUsesEnvironmentConfiguredUrl(): void
    {
        $client = self::createClient();

        $container = self::getContainer();

        $tokenResolver = $this->createMock(TokenResolver::class);
        $tokenResolver->method('resolveProjectToken')->willReturn('token');
        $container->set(TokenResolver::class, $tokenResolver);

        $citipo = $this->createMock(CitipoInterface::class);
        $citipo->method('getProject')->willReturn($this->createProject());
        $container->set(CitipoInterface::class, $citipo);

        $client->request('GET', '/legal');
        $this->assertResponseIsSuccessful();

        /** @var Environment $twig */
        $twig = $container->get('twig');
        $expectedStatsApi = $twig->getGlobals()['analytics_url'];
        $responseContent = $client->getResponse()->getContent();

        $this->assertStringContainsString('src="https://cdn.example.com/project.js"', $responseContent);
        $this->assertStringContainsString('data-stats-api="'.$expectedStatsApi.'"', $responseContent);
    }

    private function createProject(): ApiResource
    {
        $project = new ApiResource();
        $project->id = 'project-id';
        $project->name = 'Project';
        $project->locale = 'en';
        $project->metaTitle = 'Project title';
        $project->metaDescription = 'Project description';
        $project->fontTitle = 'Merriweather Sans';
        $project->fontText = 'Merriweather';
        $project->primary = '0055AA';
        $project->icon = null;
        $project->favicon = null;
        $project->sharer = null;
        $project->animateLinks = false;
        $project->animateElements = false;
        $project->captchaSiteKey = null;
        $project->captchaSecretKey = null;
        $project->tools = [];
        $project->access = ['username' => null, 'password' => null];
        $project->redirections = [];
        $project->importedRedirections = [];
        $project->terminology = ['posts' => 'Posts'];
        $project->links = [
            'stylesheet' => 'https://cdn.example.com/project.css',
            'javascript' => 'https://cdn.example.com/project.js',
            'javascript_custom' => 'https://cdn.example.com/project-custom.js',
        ];
        $project->socialSharers = [
            'facebook' => false,
            'twitter' => false,
            'bluesky' => false,
            'linkedin' => false,
            'telegram' => false,
            'whatsapp' => false,
            'email' => false,
        ];
        $project->legal = [
            'name' => 'Project Legal Name',
            'email' => 'legal@example.com',
            'address' => '123 Main Street',
            'publisherName' => 'Publisher',
            'publisherRole' => 'Director',
        ];
        $project->theme = [
            'head.html.twig' => '',
            'layout.html.twig' => '{{ header_content|raw }}{{ page_content|raw }}{{ footer_content|raw }}',
            'header.html.twig' => '',
            'footer.html.twig' => '',
            'content.html.twig' => '{{ title }}{{ content|raw }}',
        ];

        return $project;
    }
}
