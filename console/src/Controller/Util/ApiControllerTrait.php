<?php

namespace App\Controller\Util;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiControllerTrait
{
    private function createJsonApiResponse(string $message, int $status)
    {
        $data = ['status' => $status, 'message' => $message];

        return new JsonResponse($data, $status, ['Content-Type' => 'application/json']);
    }

    private function createJsonApiFormProblemResponse(FormInterface $form, int $status): JsonResponse
    {
        return $this->createJsonApiProblemResponse('Validation errors', $status, $this->createFormErrorsDetails($form));
    }

    private function createJsonApiProblemResponse(string $message, int $status, array $details = []): JsonResponse
    {
        $data = ['status' => $status, 'message' => $message, 'details' => $details];

        return new JsonResponse($data, $status, ['Content-Type' => 'application/problem+json']);
    }

    private function createFormErrorsDetails(FormInterface $form): array
    {
        if (!$form->isSubmitted()) {
            return ['form' => ['Form not submitted.']];
        }

        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $errors;
    }
}
