<?php

namespace App\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SocialLinksExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('facebook_url', [$this, 'generateFacebookUrl']),
            new TwigFunction('twitter_url', [$this, 'generateTwitterUrl']),
            new TwigFunction('linkedin_url', [$this, 'generateLinkedInUrl']),
            new TwigFunction('telegram_url', [$this, 'generateTelegramUrl']),
            new TwigFunction('whatsapp_url', [$this, 'generateWhatsAppUrl']),
            new TwigFunction('mail_url', [$this, 'generateMailUrl']),
        ];
    }

    public function generateFacebookUrl(string $name, array $params = []): string
    {
        return 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($this->generateUrlToShare($name, $params));
    }

    public function generateTwitterUrl(string $name, array $params = []): string
    {
        return 'https://twitter.com/intent/tweet?text='.$this->generateUrlToShare($name, $params);
    }

    public function generateLinkedInUrl(string $name, array $params = []): string
    {
        return 'https://www.linkedin.com/shareArticle?mini=true&url='.$this->generateUrlToShare($name, $params);
    }

    public function generateTelegramUrl(string $name, array $params = []): string
    {
        return 'https://telegram.me/share/url?url='.$this->generateUrlToShare($name, $params);
    }

    public function generateWhatsAppUrl(string $name, array $params = []): string
    {
        return 'https://wa.me/?text='.$this->generateUrlToShare($name, $params);
    }

    public function generateMailUrl(string $name, array $params = []): string
    {
        return 'mailto:?body='.$this->generateUrlToShare($name, $params);
    }

    private function generateUrlToShare(string $name, array $params = []): string
    {
        return $this->urlGenerator->generate($name, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
