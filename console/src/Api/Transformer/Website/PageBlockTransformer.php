<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\PageBlock;
use App\Repository\Website\EventRepository;
use App\Repository\Website\PostRepository;
use App\Website\CustomBlockParser;
use App\Website\PageBlock\HomeContentBlock;
use App\Website\PageBlock\HomeEventsBlock;
use App\Website\PageBlock\HomePostsBlock;

class PageBlockTransformer extends AbstractTransformer
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly PostPartialTransformer $postTransformer,
        private readonly PostCategoryTransformer $postCategoryTransformer,
        private readonly TrombinoscopePersonLightTransformer $authorTransformer,
        private readonly EventRepository $eventRepository,
        private readonly EventTransformer $eventTransformer,
        private readonly EventCategoryTransformer $eventCategoryTransformer,
        private readonly CustomBlockParser $customBlockParser,
    ) {
    }

    public function transform(PageBlock $item)
    {
        $payload = [
            '_resource' => 'PageBlock',
            'page' => $item->getPage(),
            'type' => $item->getType(),
            'config' => $item->getConfig(),
        ];

        if (HomePostsBlock::TYPE === $item->getType()) {
            $posts = $this->postRepository->getHomePosts($item->getProject(), $item->getConfig()['category'] ?? null);

            $payload['posts'] = [];
            foreach ($posts as $post) {
                $itemPayload = $this->postTransformer->transform($post);

                $itemPayload['categories'] = [];
                foreach ($post->getCategories() as $category) {
                    $itemPayload['categories'][] = $this->postCategoryTransformer->transform($category);
                }

                $itemPayload['authors'] = [];
                foreach ($post->getAuthors() as $author) {
                    $itemPayload['authors'][] = $this->authorTransformer->transform($author);
                }

                $payload['posts'][] = $itemPayload;
            }
        }

        if (HomeEventsBlock::TYPE === $item->getType()) {
            $events = $this->eventRepository->getHomeEvents($item->getProject(), $item->getConfig()['category'] ?? null);

            $payload['events'] = [];
            foreach ($events as $event) {
                $itemPayload = $this->eventTransformer->transform($event);

                $itemPayload['categories'] = [];
                foreach ($event->getCategories() as $category) {
                    $itemPayload['categories'][] = $this->eventCategoryTransformer->transform($category);
                }

                $payload['events'][] = $itemPayload;
            }
        }

        if (HomeContentBlock::TYPE === $item->getType()) {
            $payload['content'] = $this->customBlockParser->normalizeCustomBlocksIn($payload['content'] ?? '');
        }

        return $payload;
    }

    public static function describeResourceName(): string
    {
        return 'PageBlock';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'page' => 'string',
            'type' => 'string',
            'config' => [],
            'posts' => [],
        ];
    }
}
