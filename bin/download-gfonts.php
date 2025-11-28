#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Platform\Fonts;

require __DIR__.'/../console/src/Platform/Fonts.php';

$targetRoot = __DIR__.'/../console/public/fonts/gfonts';
$filesRoot = $targetRoot.'/files';

if (!is_dir($filesRoot) && !mkdir($filesRoot, 0777, true) && !is_dir($filesRoot)) {
    throw new RuntimeException(sprintf('Unable to create fonts directory at %s', $filesRoot));
}

$context = stream_context_create([
    'http' => [
        'header' => [
            'User-Agent: Mozilla/5.0 (compatible; OpenActionFontFetcher/1.0)',
            'Accept: text/css,*/*;q=0.1',
        ],
        'timeout' => 60,
    ],
]);

foreach (Fonts::getGoogleCss() as $family => $cssUrl) {
    echo sprintf("Fetching %s...\n", $family);

    $cssPath = sprintf('%s/%s.css', $targetRoot, $family);

    if (is_file($cssPath)) {
        echo "- already present, skipping download\n";

        continue;
    }

    $css = file_get_contents($cssUrl, false, $context);
    if (false === $css) {
        fwrite(STDERR, sprintf("Could not download CSS for %s from %s\n", $family, $cssUrl));
        continue;
    }

    preg_match_all('#url\\((https?://[^)]+)\\)#', $css, $matches);

    foreach (array_unique($matches[1]) as $fontUrl) {
        if (!str_starts_with($fontUrl, 'https://fonts.gstatic.com/')) {
            continue;
        }

        $path = parse_url($fontUrl, PHP_URL_PATH);
        if (!$path) {
            continue;
        }

        $destination = $filesRoot.$path;
        $destinationDir = dirname($destination);

        if (!is_dir($destinationDir) && !mkdir($destinationDir, 0777, true) && !is_dir($destinationDir)) {
            throw new RuntimeException(sprintf('Unable to create font subdirectory at %s', $destinationDir));
        }

        if (is_file($destination)) {
            continue;
        }

        $fontBinary = file_get_contents($fontUrl, false, $context);
        if (false === $fontBinary) {
            fwrite(STDERR, sprintf("Could not download font file %s\n", $fontUrl));
            continue;
        }

        file_put_contents($destination, $fontBinary);
    }

    $localCss = str_replace(['https://fonts.gstatic.com/', 'http://fonts.gstatic.com/'], 'files/', $css);
    file_put_contents($cssPath, $localCss);
}
