<?php

namespace App\Cdn;

use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;

class CdnLookup
{
    private string $projectDir;
    private ?EntrypointLookup $lookup = null;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getProjectsBaseCssVersion(): string
    {
        return abs(crc32(implode('', $this->getLookup()->getCssFiles('bundle'))));
    }

    public function getProjectsBaseCss(): string
    {
        $baseCss = '';
        foreach ($this->getLookup()->getCssFiles('bundle') as $file) {
            $baseCss .= file_get_contents($this->projectDir.'/public'.$file)."\n";
        }

        return $baseCss;
    }

    public function getProjectsBaseJavaScriptPath(): string
    {
        return $this->getLookup()->getJavaScriptFiles('bundle')[0] ?? '';
    }

    private function getLookup(): EntrypointLookup
    {
        if (!$this->lookup) {
            $this->lookup = new EntrypointLookup($this->projectDir.'/public/projects/entrypoints.json');
        }

        return $this->lookup;
    }
}
