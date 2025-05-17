<?php

namespace App\Entity\Model;

class SocialSharers
{
    public const FACEBOOK = 'facebook';
    public const TWITTER = 'twitter';
    public const BLUESKY = 'bluesky';
    public const LINKEDIN = 'linkedin';
    public const TELEGRAM = 'telegram';
    public const WHATSAPP = 'whatsapp';
    public const EMAIL = 'email';

    private array $socialSharers;

    public function __construct(array $socialSharers)
    {
        $this->socialSharers = array_intersect($socialSharers, self::getAllSocialSharers());
    }

    public function isEnabled(string $socialSharer): bool
    {
        return in_array($socialSharer, $this->socialSharers, true);
    }

    public function toArray(): array
    {
        return $this->socialSharers;
    }

    public static function getAllSocialSharers(): array
    {
        return [
            self::FACEBOOK,
            self::TWITTER,
            self::BLUESKY,
            self::LINKEDIN,
            self::TELEGRAM,
            self::WHATSAPP,
            self::EMAIL,
        ];
    }
}
