<?php

namespace App\Controller\Cdn;

use App\Cdn\CdnLookup;
use App\Controller\AbstractController;
use App\Entity\Project;
use App\Website\AssetManager;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/theme')]
class ThemeController extends AbstractController
{
    #[Route('/{uuid}.css', name: 'cdn_theme_css', stateless: true)]
    public function css(CdnLookup $lookup, AssetManager $assetManager, Project $project)
    {
        $response = new Response(trim($this->renderView('cdn/theme.css.twig', [
            'baseCss' => $lookup->getProjectsBaseCss(),
            'project' => $project,
            'projectAssets' => $assetManager->resolveApiProjectAssets($project),
            'themeAssets' => $assetManager->resolveApiThemeAssets($project),
        ])));

        $response->headers->set('Content-Type', 'text/css');

        // Cache for 1 year in the reverse proxy (there is versioning on the client side)
        $response->setCache([
            'public' => true,
            'max_age' => 31536000,
            's_maxage' => 31536000,
        ]);

        return $response;
    }

    #[Route('/{uuid}.js', name: 'cdn_theme_js', stateless: true)]
    public function js(Project $project)
    {
        $response = new Response(trim($this->renderView('cdn/theme.js.twig', [
            'project' => $project,
        ])));

        $response->headers->set('Content-Type', 'application/javascript');

        // Cache for 1 year in the reverse proxy (there is versioning on the client side)
        $response->setCache([
            'public' => true,
            'max_age' => 31536000,
            's_maxage' => 31536000,
        ]);

        return $response;
    }

    #[Route('/asset/{pathname}', requirements: ['pathname' => '.+'], name: 'cdn_theme_asset', stateless: true)]
    public function asset(FilesystemReader $cdnStorage, string $pathname)
    {
        if (str_contains($pathname, 'private')) {
            throw $this->createNotFoundException('Private upload');
        }

        if (!$cdnStorage->fileExists($pathname)) {
            throw $this->createNotFoundException('Upload file not found');
        }

        // Serve as a stream
        $response = new StreamedResponse(static function () use ($cdnStorage, $pathname) {
            stream_copy_to_stream($cdnStorage->readStream($pathname), fopen('php://output', 'wb'));
        });

        // Find file mimetype
        $response->headers->set('Content-Type', $cdnStorage->mimeType($pathname));

        // Avoid robots to index this content
        $response->headers->set('X-Robots-Tag', 'noindex');

        // Cache for 7 days in the reverse proxy
        $response->setCache([
            'public' => true,
            'max_age' => 604800,
            's_maxage' => 604800,
        ]);

        return $response;
    }
}
