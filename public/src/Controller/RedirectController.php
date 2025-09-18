<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    /**
     * @Route("/_redirect/{type}", name="redirect")
     * @Route("/_redirect/{type}/{id}", requirements={"id": ".+"}, name="redirect")
     */
    public function entityRedirect(Request $request, string $type, ?string $id = null)
    {
        switch ($type) {
            case 'home': return $this->redirectToRoute('homepage');
            case 'post': return $this->redirectToRoute('post_view', ['id' => $id, 'slug' => 'redirect']);
            case 'page': return $this->redirectToRoute('page_view', ['id' => $id, 'slug' => 'redirect']);
            case 'event': return $this->redirectToRoute('event_view', ['id' => $id, 'slug' => 'redirect']);
            case 'form': return $this->redirectToRoute('form_view', ['id' => $id, 'slug' => 'redirect']);
            case 'manifesto': return $this->redirectToRoute('manifesto_view', ['id' => $id, 'slug' => 'redirect']);
            case 'trombinoscope': return $this->redirectToRoute('trombinoscope_view', ['id' => $id, 'slug' => 'redirect']);
            case 'petition': return $this->redirectToRoute('petition_view', ['slug' => $id, 'locale' => $request->query->get('locale', 'fr')]);
            case 'manage-gdpr': return $this->redirectToRoute('manage_gdpr', ['id' => $id]);
            case 'phoning': return $this->redirectToRoute('membership_phoning_start', ['id' => $id]);
            case 'register-confirm':
                [$id, $token] = explode('/', $id);

                return $this->redirectToRoute('membership_join_confirm', ['id' => $id, 'token' => $token]);

            case 'reset-confirm':
                [$id, $token] = explode('/', $id);

                return $this->redirectToRoute('membership_reset_confirm', ['id' => $id, 'token' => $token]);

            case 'update-email-confirm':
                [$id, $token] = explode('/', $id);

                return $this->redirectToRoute('membership_area_update_email_confirm', ['id' => $id, 'token' => $token]);

            case 'unregister-confirm':
                [$id, $token] = explode('/', $id);

                return $this->redirectToRoute('membership_area_unregister_confirm', ['id' => $id, 'token' => $token]);
        }

        throw $this->createNotFoundException();
    }

    /**
     * @Route("/share/{type}/{id}/{slug}", name="share")
     */
    public function entityShare(string $type, string $id, string $slug)
    {
        switch ($type) {
            case 'post': return $this->forward(PostController::class.'::view', ['id' => $id, 'slug' => $slug]);
            case 'page': return $this->forward(PageController::class.'::view', ['id' => $id, 'slug' => $slug]);
            case 'event': return $this->forward(EventController::class.'::view', ['id' => $id, 'slug' => $slug]);
        }

        throw $this->createNotFoundException();
    }
}
