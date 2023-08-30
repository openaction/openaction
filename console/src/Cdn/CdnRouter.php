<?php

namespace App\Cdn;

use App\Entity\Upload;
use App\Repository\UploadRepository;
use Symfony\Component\Routing\RouterInterface;

class CdnRouter
{
    private string $baseUrl;
    private UploadRepository $uploadRepository;
    private RouterInterface $router;

    public function __construct(string $baseUrl, UploadRepository $uploadRepository, RouterInterface $router)
    {
        $this->baseUrl = $baseUrl;
        $this->uploadRepository = $uploadRepository;
        $this->router = $router;
    }

    /**
     * @param string|int|Upload $document
     */
    public function generateUrl($document, string $type = null): string
    {
        if (is_int($document)) {
            $document = $this->uploadRepository->find($document);
        }

        if ($document instanceof Upload) {
            $document = $document->getPathname();
        }

        $params = ['pathname' => (string) $document];
        if ($type) {
            $params['t'] = $type;
        }

        return $this->baseUrl.$this->router->generate('cdn_deliver', $params);
    }
}
