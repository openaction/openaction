<?php

namespace App\Twig;

use App\Cdn\CdnRouter;
use App\Repository\UploadRepository;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CdnExtension extends AbstractExtension
{
    private CdnRouter $router;
    private UploadRepository $uploadRepository;
    private AssetExtension $assetExtension;

    public function __construct(CdnRouter $router, UploadRepository $uploadRepository, AssetExtension $assetExtension)
    {
        $this->router = $router;
        $this->uploadRepository = $uploadRepository;
        $this->assetExtension = $assetExtension;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cdn_image_url', [$this, 'generateImageUrl']),
            new TwigFunction('cdn_url', [$this, 'generateUrl']),
        ];
    }

    public function generateImageUrl($document, string $default = 'res/images/default.jpg'): string
    {
        if (is_int($document)) {
            $document = $this->uploadRepository->find($document);
        }

        if (!$document) {
            return $this->assetExtension->getAssetUrl($default);
        }

        return $this->generateUrl($document);
    }

    public function generateUrl($document): string
    {
        return $this->router->generateUrl($document);
    }
}
