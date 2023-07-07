<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\PageBlock;
use App\Repository\Website\EventRepository;
use App\Repository\Website\PostRepository;
use App\Website\PageBlock\HomeEventsBlock;
use App\Website\PageBlock\HomePostsBlock;

class PageBlockTransformer extends AbstractTransformer
{
    private PostRepository $postRepository;
    private PostPartialTransformer $postTransformer;
    private EventRepository $eventRepository;
    private EventTransformer $eventTransformer;

    public function __construct(PostRepository $pr, PostPartialTransformer $pt, EventRepository $er, EventTransformer $et)
    {
        $this->postRepository = $pr;
        $this->postTransformer = $pt;
        $this->eventRepository = $er;
        $this->eventTransformer = $et;
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
                $itemPayload['categories'] = $this->postTransformer->includeCategories($post);

                $payload['posts'][] = $itemPayload;
            }
        }

        if (HomeEventsBlock::TYPE === $item->getType()) {
            $events = $this->eventRepository->getHomeEvents($item->getProject(), $item->getConfig()['category'] ?? null);

            $payload['events'] = [];
            foreach ($events as $event) {
                $itemPayload = $this->eventTransformer->transform($event);
                $itemPayload['categories'] = $this->eventTransformer->includeCategories($event);

                $payload['events'][] = $itemPayload;
            }
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
