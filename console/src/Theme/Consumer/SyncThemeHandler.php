<?php

namespace App\Theme\Consumer;

use App\Bridge\Github\GithubInterface;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Theme\WebsiteTheme;
use App\Entity\Theme\WebsiteThemeAsset;
use App\Platform\Themes;
use App\Theme\Source\Model\WebsiteThemeManifest;
use App\Theme\Source\ThemeManifestValidator;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SyncThemeHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private GithubInterface $github;
    private ThemeManifestValidator $manifestValidator;
    private CdnUploader $uploader;

    public function __construct(EntityManagerInterface $em, GithubInterface $g, ThemeManifestValidator $mv, CdnUploader $u)
    {
        $this->em = $em;
        $this->github = $g;
        $this->manifestValidator = $mv;
        $this->uploader = $u;
    }

    public function __invoke(SyncThemeMessage $message)
    {
        /** @var WebsiteTheme $theme */
        if (!$theme = $this->em->find(WebsiteTheme::class, $message->getThemeId())) {
            return;
        }

        // Fetch manifest
        if (!$manifestFile = $this->getFileContent($theme, 'manifest.json')) {
            $this->persistError($theme, 'The manifest.json file could not be found');

            return;
        }

        // Parse manifest
        try {
            $manifestData = Json::decode($manifestFile);
        } catch (\JsonException $e) {
            $this->persistError($theme, 'The manifest.json file could not be parsed: '.$e->getMessage());

            return;
        }

        // Validate manifest
        if ($errors = $this->manifestValidator->validate($manifestData)) {
            $this->persistError($theme, "The manifest.json file is not valid:\n".implode("\n", $errors));

            return;
        }

        $manifest = new WebsiteThemeManifest($manifestData);

        // Fetch templates
        $templates = [];
        foreach ($manifest->getTemplates() as $name => $path) {
            $content = $this->getFileContent($theme, $path);

            if (null === $content) {
                $this->persistError($theme, 'Template file '.$path.' could not be downloaded');

                return;
            }

            $templates[$name] = $content;
        }

        // Persist thumbnail
        $newThumbnail = null;
        $oldThumbnail = null;

        if ($content = $this->getFileContent($theme, $manifest->getThumbnail())) {
            $newThumbnail = $this->uploader->upload(CdnUploadRequest::createThemeThumbnailRequest(
                $this->createUploadedFile($manifest->getThumbnail(), $content)
            ));

            if ($theme->getThumbnail()) {
                $oldThumbnail = $theme->getThumbnail();
            }
        }

        // Update theme
        $theme->updateDetails($manifest->getName(), $manifest->getDescription(), $newThumbnail, $templates);
        $theme->updateDefaultColors(
            $manifest->getDefaultColors()['primary'] ?? Themes::DEFAULT_COLOR_PRIMARY,
            $manifest->getDefaultColors()['secondary'] ?? Themes::DEFAULT_COLOR_SECONDARY,
            $manifest->getDefaultColors()['third'] ?? Themes::DEFAULT_COLOR_THIRD
        );
        $theme->updateDefaultFonts(
            $manifest->getDefaultFonts()['title'] ?? Themes::DEFAULT_FONT_TITLE,
            $manifest->getDefaultFonts()['text'] ?? Themes::DEFAULT_FONT_TEXT
        );

        $theme->setUpdateError(null);
        $theme->setIsUpdating(false);

        $this->em->persist($theme);
        $this->em->flush();

        if ($newThumbnail && $oldThumbnail) {
            $this->em->remove($oldThumbnail);
            $this->em->flush();
        }

        // Remove old assets
        foreach ($theme->getAssets() as $asset) {
            $this->em->remove($asset->getFile());
            $this->em->remove($asset);
        }

        $this->em->flush();

        // Persist assets
        foreach ($manifest->getAssets() as $assetPath) {
            if (!$content = $this->getFileContent($theme, $assetPath)) {
                $this->persistError($theme, 'Asset file '.$assetPath.' could not be downloaded');

                break;
            }

            $upload = $this->uploader->upload(CdnUploadRequest::createThemeAssetRequest(
                $this->createUploadedFile($manifest->getThumbnail(), $content)
            ));

            $this->em->persist(new WebsiteThemeAsset($theme, $assetPath, $upload));
        }

        $this->em->flush();
    }

    private function getFileContent(WebsiteTheme $theme, string $pathname): ?string
    {
        return $this->github->getFileContent($theme->getInstallationId(), $theme->getRepositoryFullName(), $pathname);
    }

    private function persistError(WebsiteTheme $theme, string $error)
    {
        $theme->setUpdateError($error);
        $theme->setIsUpdating(false);

        $this->em->persist($theme);
        $this->em->flush();
    }

    private function createUploadedFile(string $pathname, string $content): UploadedFile
    {
        $file = sys_get_temp_dir().'/'.uniqid(md5($pathname), true);
        file_put_contents($file, $content);

        return new UploadedFile($file, $pathname);
    }
}
