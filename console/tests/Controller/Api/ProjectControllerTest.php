<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;

class ProjectControllerTest extends ApiTestCase
{
    public function testViewDefault()
    {
        $client = self::createClient();

        $themeDir = __DIR__.'/../../../src/DataFixtures/Resources/theme';

        $result = $this->apiRequest($client, 'GET', '/api/project');
        $this->assertApiResponse($result, [
            '_resource' => 'Project',
            '_links' => [
                'self' => 'http://localhost/api/project',
                'posts' => 'http://localhost/api/website/posts',
                'pages' => 'http://localhost/api/website/pages',
            ],
            'id' => '339h6CnXEyLWfhnuNsEvGt',
            'name' => 'Example Co',
            'locale' => 'fr',
            'fontTitle' => 'Merriweather Sans',
            'fontText' => 'Merriweather Sans',
            'theme' => [
                'head.html.twig' => file_get_contents($themeDir.'/head.html.twig'),
                'layout.html.twig' => file_get_contents($themeDir.'/layout.html.twig'),
                'header.html.twig' => file_get_contents($themeDir.'/header.html.twig'),
                'footer.html.twig' => file_get_contents($themeDir.'/footer.html.twig'),
                'list.html.twig' => file_get_contents($themeDir.'/list.html.twig'),
                'content.html.twig' => file_get_contents($themeDir.'/content.html.twig'),
                'home.html.twig' => file_get_contents($themeDir.'/home.html.twig'),
                'home-calls-to-action.html.twig' => file_get_contents($themeDir.'/home/calls-to-action.html.twig'),
                'home-custom-content.html.twig' => file_get_contents($themeDir.'/home/custom-content.html.twig'),
                'home-newsletter.html.twig' => file_get_contents($themeDir.'/home/newsletter.html.twig'),
                'home-posts.html.twig' => file_get_contents($themeDir.'/home/posts.html.twig'),
                'home-socials.html.twig' => file_get_contents($themeDir.'/home/socials.html.twig'),
                'manifesto-list.html.twig' => file_get_contents($themeDir.'/manifesto/list.html.twig'),
                'manifesto-view.html.twig' => file_get_contents($themeDir.'/manifesto/view.html.twig'),
                'trombinoscope-list.html.twig' => file_get_contents($themeDir.'/trombinoscope/list.html.twig'),
                'trombinoscope-view.html.twig' => file_get_contents($themeDir.'/trombinoscope/view.html.twig'),
            ],
            'theme_assets' => [],
            'project_assets' => [],
            'redirections' => [],
            'terminology' => [
                'posts' => 'Actualités',
                'events' => 'Événements',
                'trombinoscope' => 'Notre équipe',
                'manifesto' => 'Nos propositions',
                'newsletter' => 'Recevoir la newsletter',
                'acceptPrivacy' => 'Je consens au traitement de mes données et accepte la Politique de protection des données',
                'socialNetworks' => 'Réseaux sociaux',
            ],
            'access' => [
                'username' => null,
                'password' => null,
            ],
            'socials' => [
                'email' => 'contact@exampleco.com',
                'facebook' => 'https://facebook.com/exampleco',
                'twitter' => null,
                'snapchat' => 'exampleco',
            ],
            'socialSharers' => [
                'facebook' => true,
                'twitter' => false,
                'linkedin' => false,
                'whatsapp' => false,
                'telegram' => false,
                'email' => false,
            ],
            'membership' => [
                'introduction' => '',
                'profileFormalTitle' => 'membership.form.rule.ignore',
                'profileMiddleName' => 'membership.form.rule.ignore',
                'profileBirthdate' => 'membership.form.rule.required',
                'profileGender' => 'membership.form.rule.ignore',
                'profileNationality' => 'membership.form.rule.ignore',
                'profileCompany' => 'membership.form.rule.ignore',
                'profileJobTitle' => 'membership.form.rule.ignore',
                'contactPhone' => 'membership.form.rule.optional',
                'contactWorkPhone' => 'membership.form.rule.optional',
                'socialFacebook' => 'membership.form.rule.ignore',
                'socialTwitter' => 'membership.form.rule.ignore',
                'socialLinkedIn' => 'membership.form.rule.ignore',
                'socialTelegram' => 'membership.form.rule.ignore',
                'socialWhatsapp' => 'membership.form.rule.ignore',
                'addressStreetLine1' => 'membership.form.rule.required',
                'addressStreetLine2' => 'membership.form.rule.optional',
                'addressZipCode' => 'membership.form.rule.required',
                'addressCity' => 'membership.form.rule.required',
                'addressCountry' => 'membership.form.rule.required',
                'settingsReceiveNewsletters' => 'membership.form.rule.optional',
                'settingsReceiveSms' => 'membership.form.rule.optional',
                'settingsReceiveCalls' => 'membership.form.rule.ignore',
            ],
        ]);
    }

    public function testViewIncludesMenus()
    {
        $client = self::createClient();

        $themeDir = __DIR__.'/../../../src/DataFixtures/Resources/theme';

        $result = $this->apiRequest($client, 'GET', '/api/project?includes=header,footer', self::ACME_TOKEN);
        $this->assertApiResponse($result, [
            '_resource' => 'Project',
            '_links' => [
                'self' => 'http://localhost/api/project',
                'pages' => 'http://localhost/api/website/pages',
            ],
            'id' => '1LrlbnyOoMgF3wqx0hrobH',
            'name' => 'Acme Inc',
            'locale' => 'fr',
            'primary' => '0F345C',
            'secondary' => 'E83A7F',
            'third' => '2A568E',
            'theme' => [
                'head.html.twig' => file_get_contents($themeDir.'/head.html.twig'),
                'layout.html.twig' => file_get_contents($themeDir.'/layout.html.twig'),
                'header.html.twig' => file_get_contents($themeDir.'/header.html.twig'),
                'footer.html.twig' => file_get_contents($themeDir.'/footer.html.twig'),
                'list.html.twig' => file_get_contents($themeDir.'/list.html.twig'),
                'content.html.twig' => file_get_contents($themeDir.'/content.html.twig'),
                'home.html.twig' => file_get_contents($themeDir.'/home.html.twig'),
                'home-calls-to-action.html.twig' => file_get_contents($themeDir.'/home/calls-to-action.html.twig'),
                'home-custom-content.html.twig' => file_get_contents($themeDir.'/home/custom-content.html.twig'),
                'home-newsletter.html.twig' => file_get_contents($themeDir.'/home/newsletter.html.twig'),
                'home-posts.html.twig' => file_get_contents($themeDir.'/home/posts.html.twig'),
                'home-socials.html.twig' => file_get_contents($themeDir.'/home/socials.html.twig'),
                'manifesto-list.html.twig' => file_get_contents($themeDir.'/manifesto/list.html.twig'),
                'manifesto-view.html.twig' => file_get_contents($themeDir.'/manifesto/view.html.twig'),
                'trombinoscope-list.html.twig' => file_get_contents($themeDir.'/trombinoscope/list.html.twig'),
                'trombinoscope-view.html.twig' => file_get_contents($themeDir.'/trombinoscope/view.html.twig'),
            ],
            'theme_assets' => [
                'theme-asset.png' => 'http://localhost/serve/theme-asset.png',
            ],
            'project_assets' => [
                'asset.png' => 'http://localhost/serve/asset.png',
            ],
            'redirections' => [
                ['source' => '/redirection/dynamic/*/foo', 'target' => '/redirection/$1/1-target', 'code' => 301],
                ['source' => '/redirection/static', 'target' => '/redirection-2-target', 'code' => 302],
            ],
            'legal' => [
                'name' => 'Acme Inc SAS',
                'email' => 'gdpremail@example.com',
                'address' => 'Postal address',
                'publisherName' => 'Publisher Full Name',
                'publisherRole' => 'Publisher Role',
            ],
            'header' => [
                'data' => [
                    [
                        '_resource' => 'MenuItem',
                        'label' => 'Home',
                        'url' => '/',
                        'openNewTab' => false,
                        'children' => ['data' => []],
                    ],
                    [
                        '_resource' => 'MenuItem',
                        'label' => 'Your candidate',
                        'url' => '/page/your-candidate',
                        'openNewTab' => false,
                        'children' => [
                            'data' => [
                                [
                                    '_resource' => 'MenuItem',
                                    'label' => 'Biography',
                                    'url' => '/page/biography',
                                    'openNewTab' => true,
                                    'children' => ['data' => []],
                                ],
                                ['label' => 'Manifesto'],
                            ],
                        ],
                    ],
                    ['label' => 'Posts'],
                ],
            ],
            'footer' => [
                'data' => [
                    ['label' => 'Home'],
                    ['label' => 'Your candidate'],
                    ['label' => 'Posts'],
                    ['label' => 'Legalities'],
                    ['label' => 'Privacy policy'],
                ],
            ],
        ]);
    }

    public function testViewIncludesHome()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/project?includes=home', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'Project',
            'id' => '1LrlbnyOoMgF3wqx0hrobH',
            'name' => 'Acme Inc',
            'home' => [
                'data' => [
                    [
                        '_resource' => 'PageBlock',
                        'type' => 'cta',
                        'config' => [
                            'primary' => [
                                'label' => 'Recevoir la newsletter',
                                'target' => '/newsletter',
                                'openNewTab' => false,
                            ],
                            'secondary' => [
                                'label' => 'Lire le programme',
                                'target' => '/',
                                'openNewTab' => false,
                            ],
                        ],
                    ],
                    [
                        '_resource' => 'PageBlock',
                        'type' => 'posts',
                        'posts' => [
                            [
                                '_resource' => 'Post',
                                '_links' => [
                                    'self' => 'http://localhost/api/website/posts/12kud62vBV0tM2maNCAnl6',
                                ],
                                'id' => '12kud62vBV0tM2maNCAnl6',
                                'title' => 'On homepage',
                            ],
                        ],
                    ],
                    [
                        '_resource' => 'PageBlock',
                        'type' => 'events',
                        'events' => [],
                    ],
                    ['type' => 'newsletter'],
                    ['type' => 'socials'],
                ],
            ],
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/project', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/project', 'invalid', 401);
    }
}
