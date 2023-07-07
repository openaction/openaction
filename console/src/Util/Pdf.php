<?php

namespace App\Util;

use Symfony\Component\Process\Process;

class Pdf
{
    private string $pathname;
    private ?int $pages = null;
    private ?float $width = null;
    private ?float $height = null;
    private ?string $frontImage = null;

    private function __construct(string $pathname)
    {
        $this->pathname = $pathname;
    }

    public static function open(string $pathname): self
    {
        return new self($pathname);
    }

    public function getPages(): ?int
    {
        if (null === $this->pages) {
            $this->readMetadata();
        }

        return $this->pages;
    }

    public function getWidth(): ?int
    {
        if (null === $this->width) {
            $this->readMetadata();
        }

        return (int) round($this->width * 0.3528); // Convert to mm
    }

    public function getHeight(): ?int
    {
        if (null === $this->height) {
            $this->readMetadata();
        }

        return (int) round($this->height * 0.3528); // Convert to mm
    }

    public function getFrontPageImagePathname(): string
    {
        if (null === $this->frontImage) {
            $this->generateFrontImage();
        }

        return $this->frontImage;
    }

    private function readMetadata()
    {
        $process = new Process(['pdfinfo', $this->pathname]);
        $process->mustRun();

        $metadata = [];
        foreach (explode("\n", $process->getOutput()) as $line) {
            $lineParts = explode(':', $line);
            $key = $lineParts[0];
            unset($lineParts[0]);

            $metadata[$key] = implode(':', $lineParts);
        }

        $size = explode(' ', trim($metadata['Page size']));

        $this->pages = isset($metadata['Pages']) ? (int) trim($metadata['Pages']) : 0;
        $this->width = count($size) >= 3 ? (float) trim($size[0]) : 0;
        $this->height = count($size) >= 3 ? (float) trim($size[2]) : 0;
    }

    private function generateFrontImage()
    {
        $tmpFile = sys_get_temp_dir().'/'.Uid::random();
        touch($tmpFile);

        $process = new Process(['pdftoppm', $this->pathname, $tmpFile, '-jpeg', '-singlefile', '-f', '1', '-l', '1', '-r', '72']);
        $process->mustRun();

        $this->frontImage = $tmpFile.'.jpg';
    }
}
