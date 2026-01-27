<?php

namespace App\Command\Tools;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverDimension;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Panther\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:tools:screenshots',
    description: 'Create a screenshot of all projects on the given instance.',
)]
class ProjectsScreenshotsCommand extends Command
{
    private HttpClientInterface $httpClient;
    private FilesystemOperator $storage;
    private ImageManager $imageManager;
    private WebDriver $chromeClient;

    public function __construct(HttpClientInterface $httpClient, FilesystemOperator $toolsScreenshotsStorage, ImageManager $imageManager)
    {
        parent::__construct();

        $this->httpClient = $httpClient;
        $this->storage = $toolsScreenshotsStorage;
        $this->imageManager = $imageManager;
    }

    protected function configure()
    {
        $this
            ->addArgument('passkey', InputArgument::REQUIRED, 'Passkey to use to access projects')
            ->addOption('full', null, InputOption::VALUE_NONE, 'If enabled, not only screenshots homepages but also subpages')
            ->addOption('endpoint', null, InputOption::VALUE_REQUIRED, 'Citipo Console URL', 'https://console.citipo.com')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projects = $this->findProjects($input->getOption('endpoint'), $input->getArgument('passkey'));

        $this->chromeClient = Client::createChromeClient();
        $this->chromeClient->manage()->window()->setSize(new WebDriverDimension(1920, 1080));

        foreach ($projects as $domain => $apiToken) {
            $output->writeln("\nCreating screenshots for ".$domain);

            if ($this->storage->fileExists($domain.'.jpg')) {
                $output->writeln(' - Skipping as already built');
                continue;
            }

            // Homepage
            $output->writeln(' - Saving homepage');
            $this->tryScreenshot('https://'.$domain, $domain.'.jpg', $output);

            if (!$input->getOption('full')) {
                continue;
            }

            // Sitemap
            $output->writeln(' - Fetching sitemap');
            $sitemap = $this->fetchSitemap($input->getOption('endpoint'), $apiToken);

            // Pages
            $output->writeln(' - Saving pages');
            foreach ($sitemap['pages'] as $page) {
                $output->writeln('   - Saving page '.$page['slug'].' ('.$page['id'].')');
                $this->tryScreenshot(
                    'https://'.$domain.'/_redirect/page/'.$page['id'],
                    $domain.'/pages/'.$page['slug'].'-'.$page['id'].'.jpg',
                    $output
                );
            }

            // Posts
            $output->writeln(' - Saving posts');
            foreach ($sitemap['posts'] as $post) {
                $output->writeln('   - Saving post '.$post['slug'].' ('.$post['id'].')');
                $this->tryScreenshot(
                    'https://'.$domain.'/_redirect/post/'.$post['id'],
                    $domain.'/posts/'.$post['slug'].'-'.$post['id'].'.jpg',
                    $output
                );
            }

            // Events
            $output->writeln(' - Saving events');
            foreach ($sitemap['events'] as $event) {
                $output->writeln('   - Saving event '.$event['slug'].' ('.$event['id'].')');
                $this->tryScreenshot(
                    'https://'.$domain.'/_redirect/event/'.$event['id'],
                    $domain.'/events/'.$event['slug'].'-'.$event['id'].'.jpg',
                    $output
                );
            }
        }

        return Command::SUCCESS;
    }

    private function findProjects(string $endpoint, string $passkey)
    {
        $response = $this->httpClient->request('GET', $endpoint.'/passkey/projects', ['auth_bearer' => $passkey]);

        foreach ($response->toArray()['data'] as $item) {
            if (!str_contains($item['domain'], 'c4o.io')) {
                yield $item['domain'] => $item['token'];
            }
        }
    }

    private function fetchSitemap(string $endpoint, string $token)
    {
        return $this->httpClient->request('GET', $endpoint.'/api/project/sitemap', ['auth_bearer' => $token])->toArray();
    }

    private function tryScreenshot(string $url, string $saveAs, OutputInterface $output)
    {
        try {
            $this->screenshot($url, $saveAs);
        } catch (\Throwable) {
            $output->writeln('   Failed fetching '.$url);
        }
    }

    private function screenshot(string $url, string $saveAs)
    {
        $this->chromeClient->request('GET', $url);
        $height = $this->chromeClient->executeScript('return document.body.scrollHeight');

        $image = $this->imageManager->create(1920, (int) $height);

        // Sections of the page
        for ($i = 0; $i < $height - 1080; $i += 1080) {
            $this->chromeClient->executeScript('window.scroll(0, '.$i.')');

            $view = $this->imageManager->read($this->chromeClient->takeScreenshot());
            $image->place($view, 'top-left', 0, $i);
        }

        // Bottom of the page
        $this->chromeClient->executeScript('window.scroll(0, document.body.scrollHeight)');

        $view = $this->imageManager->read($this->chromeClient->takeScreenshot());
        $image->place($view, 'top-left', 0, $height - 1080);

        // Encode and save
        $encoded = $image->toJpeg(90);
        $this->storage->write($saveAs, (string) $encoded);
    }
}
