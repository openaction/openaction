<?php

namespace App\Cdn\Model;

class CdnUpload
{
    private string $localPathname;

    private string $storageBasePath;
    private string $storageExtension;
    private string $storageContent;

    public function __construct(string $localPathname, string $storageBasePath, string $storageExtension)
    {
        $this->localPathname = $localPathname;
        $this->storageBasePath = $storageBasePath;
        $this->storageExtension = $storageExtension;
        $this->storageContent = file_get_contents($localPathname);
    }

    public function setStorageContent(string $storageContent, string $storageExtension)
    {
        $this->storageContent = $storageContent;
        $this->storageExtension = $storageExtension;
    }

    public function getStorageFullPath(): string
    {
        return $this->storageBasePath.'.'.$this->storageExtension;
    }

    public function getStorageContent()
    {
        return $this->storageContent;
    }

    public function getLocalPathname(): string
    {
        return $this->localPathname;
    }

    public function getLocalContent(): string
    {
        return file_get_contents($this->localPathname);
    }
}
