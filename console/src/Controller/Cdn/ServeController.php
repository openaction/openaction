<?php

namespace App\Controller\Cdn;

use App\Controller\AbstractController;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemReader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ServeController extends AbstractController
{
    private FilesystemReader $storage;
    private ImageManager $imageManager;
    private Filesystem $filesystem;

    public function __construct(FilesystemReader $cdnStorage, ImageManager $imageManager, Filesystem $filesystem)
    {
        $this->storage = $cdnStorage;
        $this->imageManager = $imageManager;
        $this->filesystem = $filesystem;
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

            return $this->setResponseCache($response);
        }

        // Favicon requested: resize and reencode on the fly
        if ('favicon' === $request->query->get('t')) {
            $image = $this->imageManager->make($this->storage->read($pathname));
            $image->fit(96, 96);
            $image->encode('png');

            $response = new Response($image->getEncoded());
            $response->headers->set('Content-Type', 'image/png');

            return $this->setResponseCache($response);
        }

        // Otherwise download and serve as BinaryFileResponse to handle Range requests
        // (Safari video support requires it)
        $tmpFilename = $this->filesystem->tempnam(sys_get_temp_dir(), 'serve_');
        file_put_contents($tmpFilename, $this->storage->readStream($pathname));

        $response = new BinaryFileResponse($tmpFilename, contentDisposition: ResponseHeaderBag::DISPOSITION_INLINE);
        $response->prepare($request);
        $response->deleteFileAfterSend();

        // Find file mimetype
        $response->headers->set('Content-Type', $this->storage->mimeType($pathname));

        // Avoid robots to index this content
        $response->headers->set('X-Robots-Tag', 'noindex');

        return $this->setResponseCache($response);
    }

    private function setResponseCache(Response $response): Response
    {
        // Cache for 7 days in the reverse proxy
        $response->setCache([
            'public' => true,
            'max_age' => 604_800,
            's_maxage' => 604_800,
        ]);

        return $response;
    }
}
