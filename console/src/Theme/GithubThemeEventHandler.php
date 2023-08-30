<?php

namespace App\Theme;

use App\Entity\Theme\WebsiteTheme;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Theme\Consumer\SyncThemeMessage;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GithubThemeEventHandler
{
    private WebsiteThemeRepository $websiteThemeRepo;
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;
    private string $webhookSecret;

    public function __construct(WebsiteThemeRepository $w, EntityManagerInterface $em, MessageBusInterface $bus, string $webhookSecret)
    {
        $this->websiteThemeRepo = $w;
        $this->em = $em;
        $this->bus = $bus;
        $this->webhookSecret = $webhookSecret;
    }

    public function handleWebsiteThemeEvent(string $eventName, string $payload, string $signature): bool
    {
        if (!$this->isSignatureValid($payload, $signature)) {
            return false;
        }

        $parsed = Json::decode($payload);
        $key = isset($parsed['action']) ? $eventName.'_'.$parsed['action'] : $eventName;

        return match ($key) {
            'installation_created' => $this->handleWebsiteThemeInstallationCreated($parsed),
            'installation_deleted' => $this->handleWebsiteThemeInstallationDeleted($parsed),
            'installation_repositories_added' => $this->handleWebsiteThemeInstallationRepositoriesAdded($parsed),
            'installation_repositories_removed' => $this->handleWebsiteThemeInstallationRepositoriesRemoved($parsed),
            'push' => $this->handleWebsiteThemePush($parsed),
            default => false,
        };
    }

    private function handleWebsiteThemeInstallationCreated(array $payload): bool
    {
        foreach ($payload['repositories'] as $repo) {
            $theme = new WebsiteTheme($payload['installation']['id'], $repo['node_id'], $repo['full_name']);
            $this->em->persist($theme);
            $this->em->flush();

            $this->bus->dispatch(new SyncThemeMessage($theme->getId()));
        }

        return true;
    }

    private function handleWebsiteThemeInstallationDeleted(array $payload): bool
    {
        /** @var WebsiteTheme[] $themes */
        $themes = $this->websiteThemeRepo->findBy(['installationId' => $payload['installation']['id']]);

        foreach ($themes as $theme) {
            $theme->archive();
            $this->em->persist($theme);
        }

        $this->em->flush();

        return true;
    }

    private function handleWebsiteThemeInstallationRepositoriesAdded(array $payload): bool
    {
        foreach ($payload['repositories_added'] as $repo) {
            $theme = $this->websiteThemeRepo->findOneBy([
                'installationId' => $payload['installation']['id'],
                'repositoryNodeId' => $repo['node_id'],
            ]);

            if (!$theme) {
                $theme = new WebsiteTheme($payload['installation']['id'], $repo['node_id'], $repo['full_name']);
                $this->em->persist($theme);
                $this->em->flush();

                $this->bus->dispatch(new SyncThemeMessage($theme->getId()));
            }
        }

        return true;
    }

    private function handleWebsiteThemeInstallationRepositoriesRemoved(array $payload): bool
    {
        foreach ($payload['repositories_removed'] as $repo) {
            $theme = $this->websiteThemeRepo->findOneBy([
                'installationId' => $payload['installation']['id'],
                'repositoryNodeId' => $repo['node_id'],
            ]);

            if ($theme) {
                $theme->archive();
                $this->em->persist($theme);
            }
        }

        $this->em->flush();

        return true;
    }

    private function handleWebsiteThemePush(array $payload): bool
    {
        // Find theme
        $theme = $this->websiteThemeRepo->findOneBy([
            'installationId' => $payload['installation']['id'],
            'repositoryNodeId' => $payload['repository']['node_id'],
        ]);

        if (!$theme) {
            return false;
        }

        // Only watch default branch
        if ($payload['ref'] !== 'refs/heads/'.$payload['repository']['default_branch']) {
            return true;
        }

        $this->bus->dispatch(new SyncThemeMessage($theme->getId()));

        return true;
    }

    private function isSignatureValid(string $payload, string $signature): bool
    {
        return hash_equals('sha256='.hash_hmac('sha256', $payload, $this->webhookSecret), $signature);
    }
}
