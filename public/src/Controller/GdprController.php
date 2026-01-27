<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use App\Form\Model\UpdateGdprData;
use App\Form\UpdateGdprType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GdprController extends AbstractController
{
    #[Route('/gdpr/{id}', name: 'manage_gdpr')]
    public function manage(CitipoInterface $citipo, Request $request, string $id)
    {
        if (!$contact = $citipo->getContact($this->getApiToken(), $id)) {
            throw $this->createNotFoundException();
        }

        $data = UpdateGdprData::createFromContact($contact);

        $form = $this->createForm(UpdateGdprType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $citipo->persistContact($this->getApiToken(), $data->createApiPayload($contact->email));

            return $this->redirectToRoute('manage_gdpr', ['id' => $id, 'saved' => '1']);
        }

        return $this->render('gdpr/manage.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'saved' => $request->query->getBoolean('saved'),
        ]);
    }
}
