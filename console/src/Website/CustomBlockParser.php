<?php

namespace App\Website;

use Twig\Environment;

class CustomBlockParser
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Parse the given content to create normalized custom blocks without internal content in place
     * of Content Editor ones.
     */
    public function normalizeCustomBlocksIn(?string $content): string
    {
        if (!trim($content)) {
            return $content;
        }

        $twig = $this->twig;

        return preg_replace_callback(
            '~<div class="contenteditor-customblock"(.+)>.+<span class="contenteditor-data".+>(.+)<\/span>.+<\/div>~siU',
            static function (array $matches) use ($twig) {
                return '<div class="contenteditor-customblock"'.$matches[1].
                    ' data-contenteditor-custom-block-data-value="'.twig_escape_filter($twig, $matches[2], 'html_attr').'"></div>';
            },
            $content,
        );
    }
}
