<?php

namespace App\Command\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Project;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\File;

#[AsCommand(
    name: 'app:website:internalize-images',
    description: 'Internalize externalize images from pages and posts contents.',
)]
class InternalizeImagesCommand extends Command
{
    private Connection $db;
    private array $allowedHosts;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CdnUploader $cdnUploader,
        private readonly CdnRouter $cdnRouter,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->db = $this->entityManager->getConnection();

        // Allowed hosts are all registered domains
        $this->allowedHosts = array_column(
            $this->db->executeQuery('SELECT name FROM domains ORDER BY name')->fetchAllAssociative(),
            'name',
        );

        $projects = [];
        foreach ($this->entityManager->getRepository(Project::class)->findAll() as $project) {
            $projects[$project->getId()] = $project;
        }

        // Posts
        $io->text('Loading posts');

        $posts = $this->db
            ->executeQuery('SELECT id, project_id, content FROM website_posts WHERE content LIKE ? ORDER BY id DESC', ['%<img%'])
            ->fetchAllAssociative();

        $progress = new ProgressBar($output, max: count($posts));
        foreach ($progress->iterate($posts) as $post) {
            $this->processEntity($projects[$post['project_id']], 'posts', $post['id'], $post['content']);
        }
        $progress->finish();

        // Pages
        $io->text('Loading pages');

        $pages = $this->db
            ->executeQuery('SELECT id, project_id, content FROM website_pages WHERE content LIKE ? ORDER BY id DESC', ['%<img%'])
            ->fetchAllAssociative();

        $progress = new ProgressBar($output, max: count($pages));
        foreach ($progress->iterate($pages) as $page) {
            $this->processEntity($projects[$page['project_id']], 'pages', $page['id'], $page['content']);
        }
        $progress->finish();

        return Command::SUCCESS;
    }

    private function processEntity(Project $project, string $type, int $id, string $content): void
    {
        // Utilisation de DOMDocument pour parser le HTML
        $dom = new \DOMDocument('1.0', 'UTF-8');

        // Évite les problèmes de parsing HTML
        libxml_use_internal_errors(true);

        // Important : préfixer le contenu par un élément conteneur si le HTML n'en a pas
        $dom->loadHTML(
            '<html><body>'.$content.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        $imagesUpdated = false;

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            if (!$src) {
                continue;
            }

            // Ignore special schemes
            $scheme = parse_url($src, PHP_URL_SCHEME);
            if (in_array($scheme, ['data', 'chrome-extension'])) {
                continue;
            }

            $host = parse_url($src, PHP_URL_HOST);
            if ($this->isAllowedHost($host)) {
                continue;
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'img_');

            try {
                file_put_contents($tempPath, file_get_contents($src));
            } catch (\Throwable) {
                // Inacessible image, ignore
                continue;
            }

            try {
                $uploadRequest = CdnUploadRequest::createWebsiteImportedImageRequest($project, new File($tempPath));
                $upload = $this->cdnUploader->upload($uploadRequest);
            } catch (\Throwable) {
                // Ignore writing failures (likely due to image not being supported)
                continue;
            }

            $newUrl = $this->cdnRouter->generateUrl($upload);
            $content = str_replace($src, $newUrl, $content);
            $imagesUpdated = true;
        }

        if ($imagesUpdated) {
            $this->db->executeStatement(
                'UPDATE website_'.$type.' SET content = ? WHERE id = ?',
                [$content, $id],
            );
        }
    }

    private function isAllowedHost(?string $host): bool
    {
        if (!$host) {
            return true;
        }

        foreach ($this->allowedHosts as $allowedHost) {
            if (str_ends_with($host, $allowedHost)) {
                return true;
            }
        }

        return false;
    }
}
