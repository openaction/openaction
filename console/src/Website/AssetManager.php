<?php

namespace App\Website;

use App\Cdn\CdnRouter;
use App\Entity\Project;
use App\Repository\Theme\ProjectAssetRepository;
use App\Repository\Theme\WebsiteThemeAssetRepository;

class AssetManager
{
    private ProjectAssetRepository $projectAssetRepository;
    private WebsiteThemeAssetRepository $themeAssetRepository;
    private CdnRouter $cdn;

    public function __construct(ProjectAssetRepository $pr, WebsiteThemeAssetRepository $tr, CdnRouter $cdn)
    {
        $this->projectAssetRepository = $pr;
        $this->themeAssetRepository = $tr;
        $this->cdn = $cdn;
    }

    /**
     * Return a hash of all information related to the versionning of the appearance of a project.
     * This is used to invalidate HTTP cache on CSS/JS/... resources.
     */
    public function resolveWebsiteAppearanceVersion(Project $project): string
    {
        return md5(implode('-', [
            // Project settings
            $project->getAppearancePrimary(),
            $project->getAppearanceSecondary(),
            $project->getAppearanceThird(),
            $project->getWebsiteFontTitle(),
            $project->getWebsiteFontText(),

            // Theme
            $project->getWebsiteTheme()->getId() ?: 0,
            $project->getWebsiteTheme()->getUpdatedAt()->format('Y-m-d H:i:s') ?: '',

            // Custom CSS/JS
            $project->getWebsiteCustomCss(),
            $project->getWebsiteCustomJs(),

            // Assets
            implode('-', $this->resolveApiThemeAssets($project)),
            implode('-', $this->resolveApiProjectAssets($project)),
        ]));
    }

    public function resolveApiProjectAssets(Project $project): array
    {
        $assets = $this->projectAssetRepository->findByProject($project);

        $files = [];
        foreach ($assets as $asset) {
            $files[$asset->getName()] = $this->cdn->generateUrl($asset->getFile());
        }

        return $files;
    }

    public function resolveApiThemeAssets(Project $project): array
    {
        $assets = $this->themeAssetRepository->findByTheme($project->getWebsiteTheme());

        $files = [];
        foreach ($assets as $asset) {
            $files[$asset->getPathname()] = $this->cdn->generateUrl($asset->getFile());
        }

        return $files;
    }
}
