<?php

namespace App\Util;

class Url
{
    public static function addQueryParameter(string $url, string $queryParam, string $value): string
    {
        $parts = parse_url($url);

        // Parse existing query params
        if (isset($parts['query'])) {
            parse_str($parts['query'], $params);
        } else {
            $params = [];
        }

        $params[$queryParam] = $value;

        // Rebuild URL
        $parts['query'] = http_build_query($params);

        return implode('', [
            isset($parts['scheme']) ? $parts['scheme'].'://' : '',
            $parts['host'] ?? '',
            $parts['path'] ?? '/',
            '?',
            $parts['query'],
        ]);
    }
}
