<?php

namespace App\Controller\Cdn;

use App\Controller\AbstractController;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ServeController extends AbstractController
{
    private FilesystemReader $storage;
    private ImageManager $imageManager;

    public function __construct(FilesystemReader $cdnStorage, ImageManager $imageManager)
    {
        $this->storage = $cdnStorage;
        $this->imageManager = $imageManager;
    }

    #[Route('/serve/{pathname}', requirements: ['pathname' => '.+'], name: 'cdn_deliver', stateless: true)]
    public function serve(Request $request, string $pathname)
    {
        if (str_contains($pathname, 'private')) {
            throw $this->createNotFoundException('Private upload');
        }

        if (!$this->storage->fileExists($pathname)) {
            throw $this->createNotFoundException('Upload file not found');
        }

        // Social sharer requested: resize and reencode on the fly
        if ('sharer' === $request->query->get('t')) {
            $image = $this->imageManager->make($this->storage->read($pathname));
            $image->fit(1200, 760);
            $image->encode('jpg', 75);

            $response = new Response($image->getEncoded());
            $response->headers->set('Content-Type', 'image/jpeg');

            // Cache for 7 days in the reverse proxy
            $response->setCache([
                'public' => true,
                'max_age' => 604_800,
                's_maxage' => 604_800,
            ]);

            return $response;
        }

        // Otherwise serve as a stream
        $cdnStorage = $this->storage;
        $response = new StreamedResponse(static function () use ($cdnStorage, $pathname) {
            stream_copy_to_stream($cdnStorage->readStream($pathname), fopen('php://output', 'wb'));
        });

        // Find file mimetype
        $response->headers->set('Content-Type', $this->storage->mimeType($pathname));

        // Avoid robots to index this content
        $response->headers->set('X-Robots-Tag', 'noindex');

        // Cache for 7 days in the reverse proxy
        $response->setCache([
            'public' => true,
            'max_age' => 604_800,
            's_maxage' => 604_800,
        ]);

        return $response;
    }
}
