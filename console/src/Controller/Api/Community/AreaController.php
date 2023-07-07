<?php

namespace App\Controller\Api\Community;

use App\Api\Transformer\Website\EventTransformer;
use App\Api\Transformer\Website\FormFullTransformer;
use App\Api\Transformer\Website\PageFullTransformer;
use App\Api\Transformer\Website\PagePartialTransformer;
use App\Api\Transformer\Website\PostFullTransformer;
use App\Api\Transformer\Website\PostPartialTransformer;
use App\Community\Member\AuthorizationToken;
use App\Community\MemberAuthenticator;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Platform\Features;
use App\Repository\Website\EventRepository;
use App\Repository\Website\FormRepository;
use App\Repository\Website\PageRepository;
use App\Repository\Website\PostRepository;
use App\Util\Json;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Membership')]
#[Route('/api/community/area')]
class AreaController extends AbstractApiController
{
    use ApiControllerTrait;

    public function __construct(private MemberAuthenticator $authenticator)
    {
    }

    /**
     * Get the member-only pages list of the current project.
     *
     * This endpoint is paginated.
     */
    #[Route('/pages', name: 'api_area_pages_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of pages.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PagePartial')),
    )]
    public function pagesList(PageRepository $repository, PagePartialTransformer $transformer, Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        $currentPage = $this->apiQueryParser->getPage();
        $pages = $repository->getMembersApiPages($this->getUser(), $request->query->get('category'), $currentPage);

        return $this->handleApiCollection($pages, $transformer, false);
    }

    /**
     * Get a member-only page of the current project.
     */
    #[Route('/pages/{id}', name: 'api_area_pages_view', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the page details.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PageFull')),
    )]
    public function pagesView(PageRepository $repository, PageFullTransformer $transformer, Request $request, string $id)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        if (!$page = $repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($page, $transformer);
    }

    /**
     * Get the member-only posts list of the current project.
     *
     * This endpoint is paginated.
     */
    #[Route('/posts', name: 'api_area_posts_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of posts.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PostPartial')),
    )]
    public function postsList(PostRepository $repository, PostPartialTransformer $transformer, Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        $currentPage = $this->apiQueryParser->getPage();
        $posts = $repository->getMembersApiPosts($this->getUser(), $request->query->get('category'), $currentPage);

        return $this->handleApiCollection($posts, $transformer, true);
    }

    /**
     * Get a member-only post of the current project.
     */
    #[Route('/posts/{id}', name: 'api_area_posts_view', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the post details.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PostFull')),
    )]
    public function postsView(PostRepository $repository, PostFullTransformer $transformer, Request $request, string $id)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        if (!$post = $repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($post, $transformer);
    }

    /**
     * Get the member-only events list of the current project.
     *
     * This endpoint is paginated.
     */
    #[Route('/events', name: 'api_area_events_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of events.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Event'))
    )]
    public function eventsList(EventRepository $repository, EventTransformer $transformer, Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        $currentPage = $this->apiQueryParser->getPage();
        $events = $repository->getMembersApiEvents($this->getUser(), $request->query->get('category'), $currentPage);

        return $this->handleApiCollection($events, $transformer, true);
    }

    /**
     * Get a member-only event of the current project.
     */
    #[Route('/events/{id}', name: 'api_area_events_view', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the event details.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Event'))
    )]
    public function eventsView(EventRepository $repository, EventTransformer $transformer, Request $request, string $id)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        if (!$event = $repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($event, $transformer);
    }

    /**
     * Get the member-only forms list of the current project.
     *
     * This endpoint is paginated.
     */
    #[Route('/forms', name: 'api_area_forms_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of forms.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/FormPartial'))
    )]
    public function formsList(FormRepository $repository, FormFullTransformer $transformer, Request $request)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        $currentPage = $this->apiQueryParser->getPage();
        $forms = $repository->getMembersApiForms($this->getUser(), $currentPage);

        return $this->handleApiCollection($forms, $transformer, true);
    }

    /**
     * Get a member-only form of the current project.
     */
    #[Route('/forms/{id}', name: 'api_area_forms_view', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the form details.',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/FormFull'))
    )]
    public function formsView(FormRepository $repository, FormFullTransformer $transformer, Request $request, string $id)
    {
        $this->denyUnlessToolEnabled(Features::TOOL_MEMBERS_AREA_ACCOUNT);
        $this->denyUnlessAuthorized($request);

        if (!$form = $repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($form, $transformer);
    }

    private function denyUnlessAuthorized(Request $request)
    {
        try {
            $token = AuthorizationToken::createFromPayload(Json::decode($request->headers->get(MemberAuthenticator::TOKEN_HEADER)) ?? []);
        } catch (\Exception) {
            throw $this->createNotFoundException('Not authorized');
        }

        if (!$this->authenticator->authorize($token)) {
            throw $this->createNotFoundException('Not authorized');
        }
    }
}
