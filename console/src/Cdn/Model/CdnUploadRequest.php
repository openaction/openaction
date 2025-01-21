<?php

namespace App\Cdn\Model;

use App\Cdn\UploadHandler\ContactPictureHandler;
use App\Cdn\UploadHandler\EmailingContentImageHandler;
use App\Cdn\UploadHandler\ProjectIconImageHandler;
use App\Cdn\UploadHandler\ProjectLogoImageHandler;
use App\Cdn\UploadHandler\ProjectSharerImageHandler;
use App\Cdn\UploadHandler\ThemeThumbnailHandler;
use App\Cdn\UploadHandler\WebsiteContentImageHandler;
use App\Cdn\UploadHandler\WebsiteContentMainImageHandler;
use App\Cdn\UploadHandler\WebsiteHomeMainImageHandler;
use App\Cdn\UploadHandler\WebsiteImportedImageHandler;
use App\Cdn\UploadHandler\WebsiteManifestoMainImageHandler;
use App\Cdn\UploadHandler\WebsiteTrombinoscopeMainImageHandler;
use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\File;

class CdnUploadRequest
{
    private ?Project $project;
    private string $directory;
    private ?string $handler;
    private File $file;
    private ?string $filename;

    public function __construct(File $file, string $directory, string $handler = null, Project $project = null, string $filename = null)
    {
        $this->file = $file;
        $this->directory = $directory;
        $this->handler = $handler;
        $this->project = $project;
        $this->filename = $filename;
    }

    public static function createOrganizationPrivateFileRequest(File $file): self
    {
        return new self($file, 'private');
    }

    public static function createOrganizationEmailAutomationRequest(File $file): self
    {
        return new self($file, 'automation', EmailingContentImageHandler::class);
    }

    public static function createOrganizationWhiteLabelLogoRequest(File $file): self
    {
        return new self($file, 'organization-logo', ProjectLogoImageHandler::class);
    }

    public static function createProjectLogoRequest(Project $project, File $file): self
    {
        return new self($file, 'project-logo', ProjectLogoImageHandler::class, $project);
    }

    public static function createProjectIconRequest(Project $project, File $file): self
    {
        return new self($file, 'project-icon', ProjectIconImageHandler::class, $project);
    }

    public static function createProjectSharerRequest(Project $project, File $file): self
    {
        return new self($file, 'project-sharer', ProjectSharerImageHandler::class, $project);
    }

    public static function createWebsiteHomeMainImageRequest(Project $project, File $file): self
    {
        return new self($file, 'project-home-main', WebsiteHomeMainImageHandler::class, $project);
    }

    public static function createWebsiteHomeMainVideoRequest(Project $project, File $file): self
    {
        return new self($file, 'project-home-main', null, $project);
    }

    public static function createWebsiteContentImageRequest(Project $project, File $file): self
    {
        return new self($file, 'website-content', WebsiteContentImageHandler::class, $project);
    }

    public static function createWebsiteImportedImageRequest(Project $project, File $file): self
    {
        return new self($file, 'website-content', WebsiteImportedImageHandler::class, $project);
    }

    public static function createWebsiteContentMainImageRequest(Project $project, File $file): self
    {
        return new self($file, 'website-content-main', WebsiteContentMainImageHandler::class, $project);
    }

    public static function createWebsiteTrombinoscopeImageRequest(Project $project, File $file): self
    {
        return new self($file, 'website-trombinoscope', WebsiteTrombinoscopeMainImageHandler::class, $project);
    }

    public static function createWebsiteManifestoMainImageRequest(Project $project, File $file): self
    {
        return new self($file, 'website-manifesto', WebsiteManifestoMainImageHandler::class, $project);
    }

    public static function createWebsiteDocumentRequest(Project $project, File $file): self
    {
        return new self($file, 'website-document', null, $project);
    }

    public static function createEmailingContentRequest(Project $project, File $file): self
    {
        return new self($file, 'emailing-content', EmailingContentImageHandler::class, $project);
    }

    public static function createContactPictureRequest(File $file): self
    {
        return new self($file, 'contact-picture', ContactPictureHandler::class);
    }

    public static function createProjectAssetRequest(Project $project, File $file): self
    {
        return new self($file, 'theme-asset', null, $project);
    }

    public static function createThemeAssetRequest(File $file): self
    {
        return new self($file, 'theme-asset');
    }

    public static function createThemeThumbnailRequest(File $file): self
    {
        return new self($file, 'theme-thumbnail', ThemeThumbnailHandler::class);
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getHandler(): ?string
    {
        return $this->handler;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }
}
