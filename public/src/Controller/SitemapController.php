<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use App\Sitemap\ChangeFrequency;
use App\Sitemap\Sitemap;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    private const ROUTES_BINDINGS = [
        'pages' => [
            'route' => 'page_view',
            'bind' => ['id' => 'id', 'slug' => 'slug'],
            'changeFrequency' => ChangeFrequency::DAILY,
        ],
        'posts' => [
            'route' => 'post_view',
            'bind' => ['id' => 'id', 'slug' => 'slug'],
            'changeFrequency' => ChangeFrequency::HOURLY,
            'priority' => 0.8,
        ],
        'postCategories' => [
            'route' => 'post_list',
            'bind' => ['c' => 'id'],
            'changeFrequency' => ChangeFrequency::DAILY,
        ],
        'events' => [
            'route' => 'event_view',
            'bind' => ['id' => 'id', 'slug' => 'slug'],
            'changeFrequency' => ChangeFrequency::HOURLY,
        ],
        'eventCategories' => [
            'route' => 'event_list',
            'bind' => ['c' => 'id'],
            'changeFrequency' => ChangeFrequency::DAILY,
        ],
        'forms' => [
            'route' => 'form_view',
            'bind' => ['id' => 'id', 'slug' => 'slug'],
            'changeFrequency' => ChangeFrequency::DAILY,
        ],
        'documents' => [
            'route' => 'document_serve',
            'bind' => ['id' => 'id', 'name' => 'slug'],
            'changeFrequency' => ChangeFrequency::DAILY,
        ],
    ];

    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(CitipoInterface $citipo)
    {
        if (!$data = $citipo->getProjectSitemap($this->getApiToken())) {
            throw $this->createNotFoundException();
        }

        $sitemap = new Sitemap();

        // Add homepage
        $sitemap->add($this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL), null, ChangeFrequency::HOURLY, 1);

        // Add content
        foreach (self::ROUTES_BINDINGS as $entityType => $config) {
            foreach ($data->{$entityType} as $item) {
                $params = array_map(static fn ($value) => $item[$value], $config['bind']);

                $sitemap->add(
                    $this->generateUrl($config['route'], $params, UrlGeneratorInterface::ABSOLUTE_URL),
                    $item['updatedAt'],
                    $config['changeFrequency'],
                    0.8
                );
            }
        }

        // Add subpages
        $sitemap->add($this->generateUrl('contact_newsletter', [], UrlGeneratorInterface::ABSOLUTE_URL), null, ChangeFrequency::WEEKLY, 0.4);
        $sitemap->add($this->generateUrl('legalities', [], UrlGeneratorInterface::ABSOLUTE_URL), null, ChangeFrequency::WEEKLY, 0.2);

        return new Response($sitemap->toString());
    }
}
