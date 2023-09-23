<?php

namespace App\Controller\Api\Admin;

use App\Api\Payload\Admin\UploadContentImagePayload;
use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Controller\Api\AbstractApiController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admin')]
#[Route('/api/admin')]
class UploadController extends AbstractApiController
{
    #[Route('/uploads', name: 'api_admin_uploads_create', methods: ['POST'])]
    public function create(CdnUploader $cdnUploader, CdnRouter $cdnRouter, Request $request): Response
    {
        $payload = $this->createPayloadFromRequestFiles($request, UploadContentImagePayload::class);

        $uploadedImage = $cdnUploader->upload($payload->buildUploadRequestFor($this->getUser()));

        return new JsonResponse(
            data: ['url' => $cdnRouter->generateUrl($uploadedImage)],
            status: Response::HTTP_CREATED,
        );
    }
}
