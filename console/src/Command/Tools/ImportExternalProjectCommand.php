<?php

namespace App\Command\Tools;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Project;
use App\Entity\Website\Page;
use App\Entity\Website\PageCategory;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:tools:import-external-project',
    description: 'Import the website content of a given Citipo external project (used to synchronize on-premise with standard).',
)]
class ImportExternalProjectCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CdnUploader $uploader,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('project-uuid', InputArgument::REQUIRED, 'The project to inject the data into')
            ->addArgument('external-hostname', InputArgument::REQUIRED, 'The external API hostname (for example: console.citipo.com)')
            ->addArgument('external-token', InputArgument::REQUIRED, 'The external API project token')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist anything.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching project ...');

        /** @var Project $project */
        $project = $this->em->getRepository(Project::class)->findOneByUuid($input->getArgument('project-uuid'));
        if (!$project) {
            throw new \InvalidArgumentException('Project '.$input->getArgument('project-uuid').' does not exist.');
        }

        $dryRun = $input->getOption('dry-run');
        $apiHostname = $input->getArgument('external-hostname');
        $apiToken = $input->getArgument('external-token');

        $output->writeln('Importing pages ...');
        $this->importPages($dryRun, $apiHostname, $apiToken, $project, $output);

        $output->writeln('Importing posts ...');
        $this->importPosts($dryRun, $apiHostname, $apiToken, $project, $output);

        return Command::SUCCESS;
    }

    private function importPages(bool $dryRun, string $apiHostname, string $apiToken, Project $intoProject, OutputInterface $output)
    {
        $externalPages = $this->fetchPaginatedList($apiHostname, $apiToken, '/api/website/pages');

        foreach ($externalPages as $page) {
            $fullPage = $this->httpClient->request('GET', $page['_links']['self'], ['auth_bearer' => $apiToken])->toArray();

            // Import image
            $image = null;
            if (!$dryRun && ($imageUrl = $fullPage['image'])) {
                $image = $this->uploader->upload(
                    CdnUploadRequest::createWebsiteContentMainImageRequest($intoProject, $this->uploadUrl($imageUrl))
                );
            }

            // Import categories
            $repository = $this->em->getRepository(PageCategory::class);

            $categories = [];
            $weight = $repository->count(['project' => $intoProject]);

            foreach ($fullPage['categories']['data'] as $category) {
                // Category already exists, reuse
                if ($existing = $repository->findOneBy(['name' => $category['name'], 'project' => $intoProject])) {
                    $categories[] = $existing;

                    continue;
                }

                ++$weight;
                $category = PageCategory::createFixture([
                    'project' => $intoProject,
                    'name' => $category['name'],
                    'weight' => $weight,
                ]);

                if (!$dryRun) {
                    $this->em->persist($category);
                }

                $categories[] = $category;
            }

            $this->em->flush();

            // Page already imported
            if ($this->em->getRepository(Page::class)->findOneByBase62Uid($fullPage['id'])) {
                $output->write('S');

                continue;
            }

            if (!$dryRun) {
                $this->em->persist(Page::createFixture([
                    'uuid' => Uid::fromBase62($fullPage['id']),
                    'project' => $intoProject,
                    'title' => $fullPage['title'],
                    'slug' => $fullPage['slug'],
                    'description' => $fullPage['description'],
                    'image' => $image,
                    'content' => $fullPage['content'],
                    'categories' => $categories,
                ]));
            }

            $this->em->flush();

            $output->write('.');
        }

        $output->write("\n");
    }

    private function importPosts(bool $dryRun, string $apiHostname, string $apiToken, Project $intoProject, OutputInterface $output)
    {
        $externalPosts = $this->fetchPaginatedList($apiHostname, $apiToken, '/api/website/posts');

        foreach ($externalPosts as $post) {
            $fullPost = $this->httpClient->request('GET', $post['_links']['self'], ['auth_bearer' => $apiToken])->toArray();

            // Import image
            $image = null;
            if (!$dryRun && ($imageUrl = $fullPost['image'])) {
                $image = $this->uploader->upload(
                    CdnUploadRequest::createWebsiteContentMainImageRequest($intoProject, $this->uploadUrl($imageUrl))
                );
            }

            // Import categories
            $repository = $this->em->getRepository(PostCategory::class);

            $categories = [];
            $weight = $repository->count(['project' => $intoProject]);

            foreach ($fullPost['categories']['data'] as $category) {
                // Category already exists, reuse
                if ($existing = $repository->findOneBy(['name' => $category['name'], 'project' => $intoProject])) {
                    $categories[] = $existing;

                    continue;
                }

                ++$weight;
                $category = PostCategory::createFixture([
                    'project' => $intoProject,
                    'name' => $category['name'],
                    'weight' => $weight,
                ]);

                if (!$dryRun) {
                    $this->em->persist($category);
                }

                $categories[] = $category;
            }

            $this->em->flush();

            // Post already imported
            if ($this->em->getRepository(Post::class)->findOneByBase62Uid($fullPost['id'])) {
                $output->write('S');

                continue;
            }

            if (!$dryRun) {
                $this->em->persist(Post::createFixture([
                    'uuid' => Uid::fromBase62($fullPost['id']),
                    'project' => $intoProject,
                    'title' => $fullPost['title'],
                    'slug' => $fullPost['slug'],
                    'description' => $fullPost['description'],
                    'video' => $fullPost['video'],
                    'image' => $image,
                    'publishedAt' => new \DateTime($fullPost['published_at']),
                    'content' => $fullPost['content'],
                    'categories' => $categories,
                ]));
            }

            $this->em->flush();

            $output->write('.');
        }

        $output->write("\n");
    }

    private function fetchPaginatedList(string $apiHostname, string $apiToken, string $endpoint): array
    {
        $page = $this->httpClient->request('GET', 'https://'.$apiHostname.$endpoint, [
            'auth_bearer' => $apiToken,
        ]);

        $data = [$page->toArray()['data']];

        while ($next = $page->toArray()['meta']['pagination']['links']['next'] ?? null) {
            $page = $this->httpClient->request('GET', $next, ['auth_bearer' => $apiToken]);
            $data[] = $page->toArray()['data'];
        }

        return array_merge(...$data);
    }

    private function uploadUrl(string $url): File
    {
        $tempFile = sys_get_temp_dir().'/'.md5(uniqid('', true));
        file_put_contents($tempFile, file_get_contents($url));

        return new File($tempFile);
    }
}
