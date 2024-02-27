<?php

namespace App\Api\Transformer;

use App\Cdn\CdnRouter;
use App\Entity\Project;
use App\Entity\Website\Document;
use App\Entity\Website\Event;
use App\Entity\Website\EventCategory;
use App\Entity\Website\Form;
use App\Entity\Website\ManifestoTopic;
use App\Entity\Website\Page;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Entity\Website\TrombinoscopePerson;
use App\Platform\Features;
use App\Repository\Website\DocumentRepository;
use App\Repository\Website\EventCategoryRepository;
use App\Repository\Website\EventRepository;
use App\Repository\Website\FormRepository;
use App\Repository\Website\ManifestoTopicRepository;
use App\Repository\Website\PageRepository;
use App\Repository\Website\PostCategoryRepository;
use App\Repository\Website\PostRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;
use Symfony\Component\Uid\Uuid;

class SitemapTransformer extends AbstractTransformer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CdnRouter $cdnRouter,
    ) {
    }

    public function transform(Project $project): array
    {
        $sitemap = [
            'pages' => [],
            'posts' => [],
            'postCategories' => [],
            'events' => [],
            'eventCategories' => [],
            'forms' => [],
            'documents' => [],
            'trombinoscope' => [],
            'manifesto' => [],
        ];

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_PAGES)) {
            /** @var PageRepository $repo */
            $repo = $this->em->getRepository(Page::class);

            foreach ($repo->getApiPages($project, null) as $page) {
                $sitemap['pages'][] = $this->createNode($page->getUuid(), $page->getSlug(), $page->getUpdatedAt(), [
                    'title' => $page->getTitle(),
                    'description' => $page->getDescription(),
                    'createdAt' => $page->getCreatedAt()->format(\DateTime::ATOM),
                    'image' => $page->getImage() ? $this->cdnRouter->generateUrl($page->getImage()) : null,
                ]);
            }
        }

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_POSTS)) {
            /** @var PostRepository $repo */
            $repo = $this->em->getRepository(Post::class);

            foreach ($repo->getApiPosts($project, category: null, author: null, currentPage: 1) as $post) {
                $sitemap['posts'][] = $this->createNode($post->getUuid(), $post->getSlug(), $post->getUpdatedAt(), [
                    'title' => $post->getTitle(),
                    'description' => $post->getDescription(),
                    'createdAt' => $post->getPublishedAt()->format(\DateTime::ATOM),
                    'image' => $post->getImage() ? $this->cdnRouter->generateUrl($post->getImage()) : null,
                ]);
            }

            /** @var PostCategoryRepository $repo */
            $repo = $this->em->getRepository(PostCategory::class);

            foreach ($repo->getProjectCategories($project) as $category) {
                $sitemap['postCategories'][] = $this->createNode($category->getUuid(), $category->getSlug(), $category->getUpdatedAt(), [
                    'title' => $category->getName(),
                ]);
            }
        }

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_EVENTS)) {
            /** @var EventRepository $repo */
            $repo = $this->em->getRepository(Event::class);

            foreach ($repo->getApiEvents($project, category: null, archived: false, currentPage: 1) as $event) {
                $sitemap['events'][] = $this->createNode($event->getUuid(), $event->getSlug(), $event->getUpdatedAt(), [
                    'title' => $event->getTitle(),
                    'createdAt' => $event->getPublishedAt()->format(\DateTime::ATOM),
                    'image' => $event->getImage() ? $this->cdnRouter->generateUrl($event->getImage()) : null,
                ]);
            }

            /** @var EventCategoryRepository $repo */
            $repo = $this->em->getRepository(EventCategory::class);

            foreach ($repo->getProjectCategories($project) as $category) {
                $sitemap['eventCategories'][] = $this->createNode($category->getUuid(), $category->getSlug(), $category->getUpdatedAt(), [
                    'title' => $category->getName(),
                ]);
            }
        }

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_FORMS)) {
            /** @var FormRepository $repo */
            $repo = $this->em->getRepository(Form::class);

            foreach ($repo->getApiForms($project) as $form) {
                $sitemap['forms'][] = $this->createNode($form->getUuid(), $form->getSlug(), $form->getUpdatedAt(), [
                    'title' => $form->getTitle(),
                    'description' => $form->getDescription(),
                    'createdAt' => $form->getCreatedAt()->format(\DateTime::ATOM),
                ]);
            }
        }

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_DOCUMENTS)) {
            /** @var DocumentRepository $repo */
            $repo = $this->em->getRepository(Document::class);

            foreach ($repo->getApiDocuments($project) as $document) {
                $sitemap['documents'][] = $this->createNode($document->getUuid(), $document->getName(), $document->getUpdatedAt());
            }
        }

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_TROMBINOSCOPE)) {
            /** @var TrombinoscopePersonRepository $repo */
            $repo = $this->em->getRepository(TrombinoscopePerson::class);

            foreach ($repo->getApiPersons($project) as $person) {
                $sitemap['trombinoscope'][] = $this->createNode($person->getUuid(), $person->getSlug(), $person->getUpdatedAt(), [
                    'title' => $person->getFullName(),
                    'description' => $person->getRole(),
                    'createdAt' => $person->getPublishedAt()->format(\DateTime::ATOM),
                    'image' => $person->getImage() ? $this->cdnRouter->generateUrl($person->getImage()) : null,
                ]);
            }
        }

        if ($project->isToolEnabled(Features::TOOL_WEBSITE_MANIFESTO)) {
            /** @var ManifestoTopicRepository $repo */
            $repo = $this->em->getRepository(ManifestoTopic::class);

            foreach ($repo->getApiTopics($project) as $topic) {
                $sitemap['manifesto'][] = $this->createNode($topic->getUuid(), $topic->getSlug(), $topic->getUpdatedAt(), [
                    'title' => $topic->getTitle(),
                    'description' => $topic->getDescription(),
                    'createdAt' => $topic->getPublishedAt()->format(\DateTime::ATOM),
                    'image' => $topic->getImage() ? $this->cdnRouter->generateUrl($topic->getImage()) : null,
                ]);
            }
        }

        return $sitemap;
    }

    public static function describeResourceName(): string
    {
        return 'Sitemap';
    }

    public static function describeResourceSchema(): array
    {
        $nodesSchema = new Property([
            'type' => 'array',
            'items' => new Items([
                'type' => 'object',
                'properties' => [
                    new Property(['property' => 'id', 'type' => 'string']),
                    new Property(['property' => 'slug', 'type' => 'string']),
                    new Property(['property' => 'updatedAt', 'type' => 'string']),
                    new Property(['property' => 'title', 'type' => 'string', 'nullable' => true]),
                    new Property(['property' => 'description', 'type' => 'string', 'nullable' => true]),
                ],
            ]),
        ]);

        return [
            '_resource' => 'string',
            'pages' => clone $nodesSchema,
            'posts' => clone $nodesSchema,
            'postCategories' => clone $nodesSchema,
            'events' => clone $nodesSchema,
            'eventCategories' => clone $nodesSchema,
            'forms' => clone $nodesSchema,
            'documents' => clone $nodesSchema,
            'trombinoscope' => clone $nodesSchema,
            'manifesto' => clone $nodesSchema,
        ];
    }

    private function createNode(Uuid $uuid, string $slug, \DateTime $updatedAt, array $extra = [])
    {
        return array_merge($extra, [
            'id' => Uid::toBase62($uuid),
            'slug' => $slug,
            'updatedAt' => $updatedAt->format(\DateTime::ATOM),
        ]);
    }
}
