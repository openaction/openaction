<?php

namespace App\Website\Theme;

class ThemeFile
{
    private string $id;
    private string $section;
    private string $path;
    private string $type;
    private string $content;

    public function __construct(string $id, string $section, string $path, string $type, string $content)
    {
        $this->id = $id;
        $this->section = $section;
        $this->path = $path;
        $this->type = $type;
        $this->content = $content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
