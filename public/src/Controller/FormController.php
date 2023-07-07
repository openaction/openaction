<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use App\FormBuilder\SymfonyFormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forms")
 */
class FormController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
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

        $form = $builder->createFromBlocks($formData->blocks->data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist answer
            $answers = $builder->normalizeFormData($formData->blocks->data, $form->getData());
            $this->citipo->createFormAnswer($this->getApiToken(), $id, $answers);

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

            return $this->redirectToRoute('form_view', ['id' => $id, 'slug' => $slug, 's' => '1']);
        }

        return $this->render('forms/view.html.twig', [
            'formData' => $formData,
            'form' => $form->createView(),
            'success' => $request->query->getBoolean('s'),
        ]);
    }
}
