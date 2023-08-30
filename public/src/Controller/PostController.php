<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts")
 */
class PostController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("", name="post_list")
     */
    public function list(Request $request)
    {
        $this->denyUnlessToolEnabled('website_posts');

        $page = $request->query->getInt('p', 1);
        $category = $request->query->get('c');

        return $this->render('posts/list.html.twig', [
            'current_page' => $page,
            'current_category' => $category,
            'posts' => $this->citipo->getPosts($this->getApiToken(), $page, $category),
            'categories' => $this->citipo->getPostsCategories($this->getApiToken()),
        ]);
    }

    /**
     * @Route("/{id}/{slug}", name="post_view")
     */
    public function view(string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('website_posts');

        $post = $this->citipo->getPost($this->getApiToken(), $id);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        if ($post->slug !== $slug) {
            return $this->redirectToRoute('post_view', ['id' => $id, 'slug' => $post->slug], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('posts/view.html.twig', [
            'post' => $post,
        ]);
    }
}
