<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Laminas\Feed\Writer\Feed;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RssController extends AbstractController
{
    /**
     * @Route("/rss.xml", name="rss_feed", defaults={"_format"="xml"})
     */
    public function index(CitipoInterface $citipo)
    {
        if (!$project = $this->getProject()) {
            throw $this->createNotFoundException();
        }

        if (!$data = $citipo->getProjectSitemap($this->getApiToken())) {
            throw $this->createNotFoundException();
        }

        $feed = new Feed();
        $feed->setTitle($project->metaTitle ?: $project->name);
        $feed->setDescription($project->metaDescription ?: $project->name);
        $feed->setLink($this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $feed->setFeedLink($this->generateUrl('rss_feed', [], UrlGeneratorInterface::ABSOLUTE_URL), 'rss');

        $lastUpdatedAt = null;
        foreach ($data->posts as $post) {
            $createdAt = new \DateTime($post['createdAt']);
            $updatedAt = new \DateTime($post['updatedAt']);

            if (!$lastUpdatedAt || $lastUpdatedAt < $updatedAt) {
                $lastUpdatedAt = $updatedAt;
            }

            $entry = $feed->createEntry();
            $entry->setTitle($post['title']);
            $entry->setDateCreated($createdAt);
            $entry->setDateModified($updatedAt);
            $entry->setDescription($post['description'] ?: $post['title']);
            $entry->setLink($this->generateUrl(
                'post_view',
                ['id' => $post['id'], 'slug' => $post['slug']],
                UrlGeneratorInterface::ABSOLUTE_URL
            ));

            if ($post['image'] ?? null) {
                $entry->setEnclosure(['uri' => $post['image'], 'type' => 'image/jpeg']);
            }

            $feed->addEntry($entry);
        }

        $feed->setDateModified($lastUpdatedAt);

        return new Response($feed->export('rss'));
    }
}
