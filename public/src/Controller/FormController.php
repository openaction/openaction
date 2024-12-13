<?php

namespace App\Controller;

use App\Bridge\Turnstile\Turnstile;
use App\Client\CitipoInterface;
use App\FormBuilder\SymfonyFormBuilder;
use App\Util\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forms")
 */
class FormController extends AbstractController
{
    public function __construct(
        private readonly Turnstile $turnstile,
        private readonly CitipoInterface $citipo,
    ) {
    }

    /**
     * @Route("/{id}/{slug}", name="form_view")
     */
    public function view(SymfonyFormBuilder $builder, Request $request, string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('website_forms');

        $formData = $this->citipo->getForm($this->getApiToken(), $id);

        if (!$formData) {
            throw $this->createNotFoundException();
        }

        if ($formData->slug !== $slug) {
            return $this->redirectToRoute('form_view', ['id' => $id, 'slug' => $formData->slug], Response::HTTP_MOVED_PERMANENTLY);
        }

        $challenge = $this->turnstile->createCaptchaChallenge($this->getProject());

        $form = $builder->createFromBlocks($formData->blocks->data, [], $this->getProject()->enableGdprFields);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($challenge && !$challenge->isValidResponse($request->request->get('cf-turnstile-response'))) {
                return $this->redirectToRoute('form_view', ['id' => $id, 'slug' => $slug]);
            }

            // Persist answer
            $answers = $builder->normalizeFormData($formData->blocks->data, $form->getData());
            $persistedAnswer = $this->citipo->createFormAnswer($this->getApiToken(), $id, $answers);

            // Persist picture if requested
            $email = $builder->getEmailValue($formData->blocks->data, $form->getData());
            $picture = $builder->getPictureValue($formData->blocks->data, $form->getData());

            if ($email && $picture) {
                $this->citipo->persistContactPicture(
                    $this->getApiToken(),
                    $this->citipo->getContactStatus($this->getApiToken(), $email)->id,
                    $picture
                );
            }

            if ($formData->redirectUrl) {
                return $this->redirect(Url::addQueryParameter($formData->redirectUrl, 'answerId', $persistedAnswer->id));
            }

            return $this->redirectToRoute('form_view', ['id' => $id, 'slug' => $slug, 's' => '1']);
        }

        return $this->render('forms/view.html.twig', [
            'formData' => $formData,
            'form' => $form->createView(),
            'captcha_challenge' => $challenge,
            'success' => $request->query->getBoolean('s'),
        ]);
    }
}
