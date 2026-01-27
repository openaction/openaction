<?php

namespace App\Controller\Membership;

use App\Client\CitipoInterface;
use App\FormBuilder\SymfonyFormBuilder;
use App\Security\CookieManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/members/area')]
class ContentController extends AbstractMembershipController
{
    #[Route('/dashboard', name: 'membership_area_dashboard')]
    public function dashboard(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$project = $this->getProject()) {
            throw $this->createNotFoundException();
        }

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        return $this->render('member/area/dashboard.html.twig', [
            'contact' => $contact,
            'page' => $project->membershipMainPage,
        ]);
    }

    #[Route('/resources', name: 'membership_area_resources')]
    public function resources(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_resources');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        return $this->render('member/area/resources.html.twig', [
            'pages' => $this->container->get(CitipoInterface::class)->getMembersPages(
                $this->getApiToken(),
                $this->container->get(CookieManager::class)->readAuthToken($request)
            ),
        ]);
    }

    #[Route('/resources/page/{id}/{slug}', name: 'membership_area_resources_page_view')]
    public function pageView(Request $request, string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('members_area_resources');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $authToken = $this->container->get(CookieManager::class)->readAuthToken($request);

        $page = $this->container->get(CitipoInterface::class)->getMembersPage($this->getApiToken(), $authToken, $id);
        if (!$page) {
            throw $this->createNotFoundException();
        }

        if ($page->slug !== $slug) {
            return $this->redirectToRoute(
                'membership_area_resources_page_view',
                ['id' => $id, 'slug' => $page->slug],
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        return $this->render('member/area/page.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/posts', name: 'membership_area_posts')]
    public function posts(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_posts');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $page = $request->query->getInt('p', 1);

        return $this->render('member/area/posts.html.twig', [
            'current_page' => $page,
            'posts' => $this->container->get(CitipoInterface::class)->getMembersPosts(
                $this->getApiToken(),
                $this->container->get(CookieManager::class)->readAuthToken($request),
                $page
            ),
        ]);
    }

    #[Route('/posts/{id}/{slug}', name: 'membership_area_post_view')]
    public function postView(Request $request, string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('members_area_posts');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $authToken = $this->container->get(CookieManager::class)->readAuthToken($request);

        $post = $this->container->get(CitipoInterface::class)->getMembersPost($this->getApiToken(), $authToken, $id);
        if (!$post) {
            throw $this->createNotFoundException();
        }

        if ($post->externalUrl) {
            return $this->redirect($post->externalUrl);
        }

        if ($post->slug !== $slug) {
            return $this->redirectToRoute(
                'membership_area_post_view',
                ['id' => $id, 'slug' => $post->slug],
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        return $this->render('member/area/post.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/events', name: 'membership_area_events')]
    public function events(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_events');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $page = $request->query->getInt('p', 1);

        return $this->render('member/area/events.html.twig', [
            'current_page' => $page,
            'events' => $this->container->get(CitipoInterface::class)->getMembersEvents(
                $this->getApiToken(),
                $this->container->get(CookieManager::class)->readAuthToken($request),
                $page
            ),
        ]);
    }

    #[Route('/events/{id}/{slug}', name: 'membership_area_event_view')]
    public function eventView(Request $request, string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('members_area_events');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $authToken = $this->container->get(CookieManager::class)->readAuthToken($request);

        $event = $this->container->get(CitipoInterface::class)->getMembersEvent($this->getApiToken(), $authToken, $id);
        if (!$event) {
            throw $this->createNotFoundException();
        }

        if ($event->externalUrl) {
            return $this->redirect($event->externalUrl);
        }

        if ($event->slug !== $slug) {
            return $this->redirectToRoute(
                'membership_area_event_view',
                ['id' => $id, 'slug' => $event->slug],
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        return $this->render('member/area/event.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/forms', name: 'membership_area_forms')]
    public function forms(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_forms');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $page = $request->query->getInt('p', 1);

        return $this->render('member/area/forms.html.twig', [
            'current_page' => $page,
            'forms' => $this->container->get(CitipoInterface::class)->getMembersForms(
                $this->getApiToken(),
                $this->container->get(CookieManager::class)->readAuthToken($request),
                $page
            ),
        ]);
    }

    #[Route('/forms/{id}/{slug}', name: 'membership_area_form_view')]
    public function formView(SymfonyFormBuilder $builder, Request $request, string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('members_area_forms');

        /** @var CitipoInterface $citipo */
        $citipo = $this->container->get(CitipoInterface::class);

        $authToken = $this->container->get(CookieManager::class)->readAuthToken($request);
        $formData = $citipo->getMembersForm($this->getApiToken(), $authToken, $id);

        if (!$formData) {
            throw $this->createNotFoundException();
        }

        if ($formData->slug !== $slug) {
            return $this->redirectToRoute(
                'membership_area_form_view',
                ['id' => $id, 'slug' => $formData->slug],
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        $form = $builder->createFromBlocks($formData->blocks->data, [], $this->getProject()->enableGdprFields);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answers = $builder->normalizeFormData($formData->blocks->data, $form->getData());
            $citipo->createFormAnswer($this->getApiToken(), $id, $answers, $authToken);

            return $this->redirectToRoute('membership_area_form_view', ['id' => $id, 'slug' => $slug, 's' => '1']);
        }

        return $this->render('member/area/form.html.twig', [
            'formData' => $formData,
            'form' => $form->createView(),
            'success' => $request->query->getBoolean('s'),
        ]);
    }
}
