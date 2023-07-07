<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\WebpackEncoreBundle\Twig\StimulusTwigExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EmbedConsentExtension extends AbstractExtension
{
    private bool $enableEmbedConsent;
    private StimulusTwigExtension $stimulus;
    private TranslatorInterface $translator;

    private ?Environment $env = null;

    public function __construct(bool $enableEmbedConsent, StimulusTwigExtension $stimulus, TranslatorInterface $translator)
    {
        $this->enableEmbedConsent = $enableEmbedConsent;
        $this->stimulus = $stimulus;
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('apply_embed_consent', [$this, 'applyEmbedConsent'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function applyEmbedConsent(Environment $env, ?string $content): string
    {
        $this->env = $env;

        $content = trim($content ?: '');

        // Google Maps
        $content = preg_replace_callback(
            '~<iframe.*src="(.+google.com/maps.+)".*></iframe>~U',
            [$this, 'replaceGoogleMapIframe'],
            $content
        );

        // Youtube
        $content = preg_replace_callback(
            '~<iframe.*src="(.+youtube.com/embed.+)".*></iframe>~U',
            [$this, 'replaceYoutubeIframe'],
            $content
        );

        // Twitter tweet
        $content = preg_replace_callback(
            '~(<blockquote.*class="twitter-tweet".*>.+<a.*href="(.*)">.*</blockquote>)\s*<script .+></script>~U',
            [$this, 'replaceTwitterTweet'],
            $content
        );

        // Facebook post
        $content = preg_replace_callback(
            '~<iframe.*src="(.+facebook.com/plugins/post.php.+)".*></iframe>~U',
            [$this, 'replaceFacebookPostIframe'],
            $content
        );

        return $content;
    }

    public function replaceGoogleMapIframe(array $matches): string
    {
        return $this->renderEmbedConsent(['type' => 'google-map', 'url' => $matches[1]]);
    }

    public function replaceYoutubeIframe(array $matches): string
    {
        return $this->renderEmbedConsent(['type' => 'youtube-video', 'url' => $matches[1]]);
    }

    public function replaceTwitterTweet(array $matches): string
    {
        return $this->renderEmbedConsent(['type' => 'twitter-tweet', 'url' => $matches[2]], $matches[1]);
    }

    public function replaceFacebookPostIframe(array $matches): string
    {
        return $this->renderEmbedConsent(['type' => 'facebook-post', 'url' => $matches[1]]);
    }

    private function renderEmbedConsent(array $values, string $html = ''): string
    {
        $values = array_merge($values, [
            'titleLabel' => $this->translator->trans('base.embed-consent.title'),
            'descriptionLabel' => $this->translator->trans('base.embed-consent.description'),
            'acceptLabel' => $this->translator->trans('base.embed-consent.accept'),
            'externalLabel' => $this->translator->trans('base.embed-consent.external'),
            'cancelLabel' => $this->translator->trans('base.embed-consent.cancel'),
            'enableConsent' => $this->enableEmbedConsent,
        ]);

        return '<div '.$this->stimulus->renderStimulusController($this->env, 'embed-consent', $values).'>'.$html.'</div>';
    }
}
