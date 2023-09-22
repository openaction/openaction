<?php

namespace App\Util;

class Video
{
    private const SUPPORTED_PROVIDERS = ['facebook', 'youtube'];

    public function __construct(private readonly string $provider, private readonly string $id)
    {
    }

    public function toProviderUrl(): string
    {
        return match ($this->provider) {
            'facebook' => 'https://www.facebook.com/watch/?v='.$this->id,
            'youtube' => 'https://www.youtube.com/watch?v='.$this->id,
            default => '',
        };
    }

    public function toReference(): string
    {
        return $this->provider.':'.$this->id;
    }

    public static function fromReference(?string $reference): ?self
    {
        if (!$reference || !is_string($reference)) {
            return null;
        }

        if (2 !== count($parts = explode(':', $reference))) {
            return null;
        }

        if (!in_array($parts[0], self::SUPPORTED_PROVIDERS)) {
            return null;
        }

        return new self($parts[0], $parts[1]);
    }

    public static function createFromUrl(?string $url): ?self
    {
        if (!$url || !is_string($url)) {
            return null;
        }

        // youtube.com long URL
        if (str_contains($url, 'youtube.com')) {
            $params = [];
            parse_str(parse_url($url, PHP_URL_QUERY), $params);

            return !empty($params['v']) ? new self('youtube', $params['v']) : null;
        }

        // youtu.be short URL
        if (str_contains($url, 'youtu.be')) {
            $id = trim(parse_url($url, PHP_URL_PATH), '/');

            return $id ? new self('youtube', $id) : null;
        }

        // facebook.com long URL
        if (str_contains($url, 'facebook.com')) {
            $params = [];
            parse_str(parse_url($url, PHP_URL_QUERY), $params);

            return !empty($params['v']) ? new self('facebook', $params['v']) : null;
        }

        // fb.watch short URL
        if (str_contains($url, 'fb.watch')) {
            $id = trim(parse_url($url, PHP_URL_PATH), '/');

            return $id ? new self('facebook', $id) : null;
        }

        return null;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
