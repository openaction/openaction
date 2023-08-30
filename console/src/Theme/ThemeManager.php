<?php

namespace App\Theme;

use App\Entity\Project;
use App\Website\Theme\ThemeFile;
use Symfony\Component\HttpFoundation\Request;

class ThemeManager
{
    private const TEMPLATES = [
        'website_head' => 'head.html.twig',
        'website_layout' => 'layout.html.twig',
        'website_header' => 'header.html.twig',
        'website_footer' => 'footer.html.twig',
        'website_list' => 'list.html.twig',
        'website_content' => 'content.html.twig',
        'website_home' => 'home.html.twig',
        'website_home_calls_to_action' => 'home-calls-to-action.html.twig',
        'website_home_custom_content' => 'home-custom-content.html.twig',
        'website_home_newsletter' => 'home-newsletter.html.twig',
        'website_home_posts' => 'home-posts.html.twig',
        'website_home_events' => 'home-events.html.twig',
        'website_home_socials' => 'home-socials.html.twig',
        'website_manifesto_list' => 'manifesto-list.html.twig',
        'website_manifesto_view' => 'manifesto-view.html.twig',
        'website_trombinoscope_list' => 'trombinoscope-list.html.twig',
        'website_trombinoscope_view' => 'trombinoscope-view.html.twig',
    ];

    private const RESOLVING = [
        'style.css.twig' => 'style',
        'script.js.twig' => 'script',
        'head.html.twig' => 'head',
        'layout.html.twig' => 'layout',
        'header.html.twig' => 'header',
        'footer.html.twig' => 'footer',
        'list.html.twig' => 'list',
        'content.html.twig' => 'content',
        'home.html.twig' => 'home',
        'home-calls-to-action.html.twig' => 'home-calls-to-action',
        'home-custom-content.html.twig' => 'home-custom-content',
        'home-newsletter.html.twig' => 'home-newsletter',
        'home-posts.html.twig' => 'home-posts',
        'home-events.html.twig' => 'home-events',
        'home-socials.html.twig' => 'home-socials',
        'manifesto-list.html.twig' => 'manifesto-list',
        'manifesto-view.html.twig' => 'manifesto-view',
        'trombinoscope-list.html.twig' => 'trombinoscope-list',
        'trombinoscope-view.html.twig' => 'trombinoscope-view',
    ];

    /**
     * @return ThemeFile[]
     */
    public function getThemeFiles(Project $project): array
    {
        return [
            // Website
            new ThemeFile('website_style', 'website', 'style.css', 'css', $project->getWebsiteCustomCss() ?: ''),
            new ThemeFile('website_script', 'website', 'script.js', 'javascript', $project->getWebsiteCustomJs() ?: ''),
            new ThemeFile('website_head', 'website', 'head.html.twig', 'xml', $this->resolveTemplate($project, 'head.html.twig')),
            new ThemeFile('website_layout', 'website', 'layout.html.twig', 'xml', $this->resolveTemplate($project, 'layout.html.twig')),
            new ThemeFile('website_header', 'website', 'header.html.twig', 'xml', $this->resolveTemplate($project, 'header.html.twig')),
            new ThemeFile('website_footer', 'website', 'footer.html.twig', 'xml', $this->resolveTemplate($project, 'footer.html.twig')),
            new ThemeFile('website_content', 'website', 'content.html.twig', 'xml', $this->resolveTemplate($project, 'content.html.twig')),
            new ThemeFile('website_list', 'website', 'list.html.twig', 'xml', $this->resolveTemplate($project, 'list.html.twig')),
            new ThemeFile('website_home', 'website', 'home.html.twig', 'xml', $this->resolveTemplate($project, 'home.html.twig')),
            new ThemeFile('website_home_calls_to_action', 'website', 'home-calls-to-action.html.twig', 'xml', $this->resolveTemplate($project, 'home-calls-to-action.html.twig')),
            new ThemeFile('website_home_custom_content', 'website', 'home-custom-content.html.twig', 'xml', $this->resolveTemplate($project, 'home-custom-content.html.twig')),
            new ThemeFile('website_home_newsletter', 'website', 'home-newsletter.html.twig', 'xml', $this->resolveTemplate($project, 'home-newsletter.html.twig')),
            new ThemeFile('website_home_posts', 'website', 'home-posts.html.twig', 'xml', $this->resolveTemplate($project, 'home-posts.html.twig')),
            new ThemeFile('website_home_events', 'website', 'home-events.html.twig', 'xml', $this->resolveTemplate($project, 'home-events.html.twig')),
            new ThemeFile('website_home_socials', 'website', 'home-socials.html.twig', 'xml', $this->resolveTemplate($project, 'home-socials.html.twig')),
            new ThemeFile('website_manifesto_list', 'website', 'manifesto-list.html.twig', 'xml', $this->resolveTemplate($project, 'manifesto-list.html.twig')),
            new ThemeFile('website_manifesto_view', 'website', 'manifesto-view.html.twig', 'xml', $this->resolveTemplate($project, 'manifesto-view.html.twig')),
            new ThemeFile('website_trombinoscope_list', 'website', 'trombinoscope-list.html.twig', 'xml', $this->resolveTemplate($project, 'trombinoscope-list.html.twig')),
            new ThemeFile('website_trombinoscope_view', 'website', 'trombinoscope-view.html.twig', 'xml', $this->resolveTemplate($project, 'trombinoscope-view.html.twig')),

            // Emailing
            new ThemeFile('emailing_style', 'emailing', 'style.css', 'css', $project->getEmailingCustomCss() ?: ''),
            new ThemeFile('emailing_legalities', 'emailing', 'legalities.html.twig', 'xml', $project->getEmailingLegalities() ?: ''),
        ];
    }

    public function applyThemeChanges(Project $project, Request $request)
    {
        $project->applyWebsiteCssUpdate($request->request->get('website_style', ''));
        $project->applyWebsiteJsUpdate($request->request->get('website_script', ''));
        $project->applyEmailingCssUpdate($request->request->get('emailing_style', ''));
        $project->applyEmailingLegalitiesUpdate($request->request->get('emailing_legalities', ''));

        // Only save templates that are different from the core templates
        foreach (self::TEMPLATES as $id => $filename) {
            $content = $request->request->get($id);
            if (!$content || $this->isSameAsCore($project, $filename, $content)) {
                $content = null;
            }

            $project->applyWebsiteTemplatesUpdate($filename, $content);
        }
    }

    public function resolveApiTemplates(Project $project): array
    {
        $files = [];
        foreach (self::TEMPLATES as $filename) {
            $files[$filename] = $this->resolveTemplate($project, $filename);
        }

        return $files;
    }

    private function resolveTemplate(Project $project, string $filename): string
    {
        $customTemplates = $project->getWebsiteCustomTemplates();

        if (!empty($customTemplates[$filename])) {
            return $customTemplates[$filename];
        }

        return $this->resolveCoreTemplate($project, $filename);
    }

    private function isSameAsCore(Project $project, string $filename, string $userContent): bool
    {
        $normalizedContent = str_replace(["\r", "\n", "\t"], '', $userContent);
        $normalizedCore = str_replace(["\r", "\n", "\t"], '', $this->resolveCoreTemplate($project, $filename));

        return trim($normalizedContent) === trim($normalizedCore);
    }

    private function resolveCoreTemplate(Project $project, string $filename): string
    {
        return $project->getWebsiteTheme()->getTemplates()[self::RESOLVING[$filename]] ?? '';
    }
}
