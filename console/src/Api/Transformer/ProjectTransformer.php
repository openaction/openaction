<?php

namespace App\Api\Transformer;

use App\Api\Transformer\Website\PageBlockTransformer;
use App\Cdn\CdnLookup;
use App\Cdn\CdnRouter;
use App\Entity\Model\SocialSharers;
use App\Entity\Project;
use App\Entity\Website\MenuItem;
use App\Platform\Features;
use App\Repository\Website\MenuItemRepository;
use App\Repository\Website\PageBlockRepository;
use App\Repository\Website\RedirectionRepository;
use App\Theme\ThemeManager;
use App\Util\Uid;
use App\Website\AssetManager;
use App\Website\PageBlock\BlockInterface;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class ProjectTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['header', 'footer', 'home'];

    // Local cache for includes
    private array $cache = [];

    public function __construct(
        private readonly ThemeManager $themeManager,
        private readonly AssetManager $assetManager,
        private readonly CdnLookup $cdnLookup,
        private readonly CdnRouter $cdnRouter,
        private readonly RedirectionRepository $redirectionRepository,
        private readonly MenuItemRepository $menuItemRepository,
        private readonly PageBlockRepository $blockRepository,
        private readonly PageBlockTransformer $homeBlockTranformer,
    ) {
    }

    public function transform(Project $project)
    {
        $uuid = $project->getUuid()->toRfc4122();
        $cssVersion = $this->cdnLookup->getProjectsBaseCssVersion();
        $appearanceVersion = $this->assetManager->resolveWebsiteAppearanceVersion($project);

        return [
            '_resource' => 'Project',
            '_links' => [
                'self' => $this->createLink('api_project'),
                'posts' => $this->createLink('api_website_posts_list'),
                'pages' => $this->createLink('api_website_pages_list'),
                'events' => $this->createLink('api_website_events_list'),
                'stylesheet' => $this->createLink('cdn_theme_css', ['uuid' => $uuid, 'b' => $cssVersion, 'v' => $appearanceVersion]),
                'javascript' => $this->createAssetUrl($project, 'root_url').$this->cdnLookup->getProjectsBaseJavaScriptPath(),
                'javascript_custom' => $this->createAssetUrl($project, 'cdn_theme_js', ['uuid' => $uuid, 'v' => $appearanceVersion]),
                'analytics' => $this->createAssetUrl($project, 'root_url').'/projects/event',
            ],
            'uuid' => $project->getUuid()->toRfc4122(),
            'id' => Uid::toBase62($project->getUuid()),
            'name' => $project->getName(),
            'locale' => $project->getWebsiteLocale(),
            'domain' => $project->getFullDomain(),
            'logoDark' => $project->getAppearanceLogoDark() ? $this->cdnRouter->generateUrl($project->getAppearanceLogoDark()) : null,
            'logoWhite' => $project->getAppearanceLogoWhite() ? $this->cdnRouter->generateUrl($project->getAppearanceLogoWhite()) : null,
            'icon' => $project->getAppearanceIcon() ? $this->cdnRouter->generateUrl($project->getAppearanceIcon()) : null,
            'favicon' => $project->getAppearanceIcon() ? $this->cdnRouter->generateUrl($project->getAppearanceIcon(), type: 'favicon') : null,
            'sharer' => $project->getWebsiteSharer() ? $this->cdnRouter->generateUrl($project->getWebsiteSharer()) : null,
            'primary' => $project->getAppearancePrimary(),
            'secondary' => $project->getAppearanceSecondary(),
            'third' => $project->getAppearanceThird(),
            'fontTitle' => $project->getWebsiteFontTitle() ?: 'Merriweather Sans',
            'fontText' => $project->getWebsiteFontText() ?: 'Merriweather',
            'metaTitle' => $project->getWebsiteMetaTitle() ?: '',
            'metaDescription' => $project->getWebsiteMetaDescription() ?: '',
            'mainImage' => $project->getWebsiteMainImage() ? $this->cdnRouter->generateUrl($project->getWebsiteMainImage()) : null,
            'mainVideo' => $project->getWebsiteMainVideo() ? $this->cdnRouter->generateUrl($project->getWebsiteMainVideo()) : null,
            'introPosition' => $project->getWebsiteMainIntroPosition(),
            'introOverlay' => $project->hasWebsiteMainIntroOverlay(),
            'introTitle' => $project->getWebsiteMainIntroTitle(),
            'introContent' => $project->getWebsiteMainIntroContent(),
            'animateElements' => $project->isWebsiteAnimateElements(),
            'animateLinks' => $project->isWebsiteAnimateLinks(),
            'terminology' => $project->getAppearanceTerminology()->toArray(),
            'theme' => $this->themeManager->resolveApiTemplates($project),
            'theme_assets' => $this->assetManager->resolveApiThemeAssets($project),
            'project_assets' => $this->assetManager->resolveApiProjectAssets($project),
            'redirections' => $this->redirectionRepository->getApiRedirections($project),
            'tools' => $project->getAccessibleTools(),
            'access' => [
                'username' => $project->getWebsiteAccessUser(),
                'password' => $project->getWebsiteAccessPass(),
            ],
            'socials' => [
                'email' => $project->getSocialEmail(),
                'phone' => $project->getSocialPhone(),
                'facebook' => $project->getSocialFacebook(),
                'twitter' => $project->getSocialTwitter(),
                'instagram' => $project->getSocialInstagram(),
                'linkedin' => $project->getSocialLinkedIn(),
                'youtube' => $project->getSocialYoutube(),
                'medium' => $project->getSocialMedium(),
                'telegram' => $project->getSocialTelegram(),
                'snapchat' => $project->getSocialSnapchat(),
                'whatsapp' => $project->getSocialWhatsapp(),
                'tiktok' => $project->getSocialTiktok(),
                'threads' => $project->getSocialThreads(),
            ],
            'socialSharers' => [
                'facebook' => $project->getSocialSharers()->isEnabled(SocialSharers::FACEBOOK),
                'twitter' => $project->getSocialSharers()->isEnabled(SocialSharers::TWITTER),
                'linkedin' => $project->getSocialSharers()->isEnabled(SocialSharers::LINKEDIN),
                'telegram' => $project->getSocialSharers()->isEnabled(SocialSharers::TELEGRAM),
                'whatsapp' => $project->getSocialSharers()->isEnabled(SocialSharers::WHATSAPP),
                'email' => $project->getSocialSharers()->isEnabled(SocialSharers::EMAIL),
            ],
            'legal' => [
                'name' => $project->getLegalGdprName(),
                'email' => $project->getLegalGdprEmail(),
                'address' => $project->getLegalGdprAddress(),
                'publisherName' => $project->getLegalPublisherName(),
                'publisherRole' => $project->getLegalPublisherRole(),
            ],
            'membership' => $project->getMembershipFormSettings()->toArray(),
            'membershipMainPage' => $project->getMembershipMainPage(),
            'captchaSiteKey' => $project->getWebsiteTurnstileSiteKey(),
            'captchaSecretKey' => $project->getWebsiteTurnstileSecretKey(),
            'enableGdprFields' => !$project->getWebsiteDisableGdprFields(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Project';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
                'posts' => 'string',
                'pages' => 'string',
                'events' => 'string',
                'stylesheet' => 'string',
                'javascript' => 'string',
                'javascript_custom' => 'string',
                'analytics' => 'string',
            ], 'id' => '?string',
            'name' => '?string',
            'locale' => '?string',
            'domain' => '?string',
            'logoDark' => '?string',
            'logoWhite' => '?string',
            'icon' => '?string',
            'sharer' => '?string',
            'primary' => '?string',
            'secondary' => '?string',
            'third' => '?string',
            'fontTitle' => '?string',
            'fontText' => '?string',
            'metaTitle' => '?string',
            'metaDescription' => '?string',
            'mainImage' => '?string',
            'introPosition' => '?string',
            'introOverlay' => '?boolean',
            'introTitle' => '?string',
            'introContent' => '?string',
            'animateElements' => '?boolean',
            'animateLinks' => '?boolean',
            'terminology' => [
                'posts' => '?string',
                'events' => '?string',
                'trombinoscope' => '?string',
                'manifesto' => '?string',
                'newsletter' => '?string',
                'socialNetworks' => '?string',
            ],
            'theme' => [
                'head.html.twig' => '?string',
                'layout.html.twig' => '?string',
                'header.html.twig' => '?string',
                'footer.html.twig' => '?string',
                'list.html.twig' => '?string',
                'content.html.twig' => '?string',
                'home.html.twig' => '?string',
                'home-calls-to-action.html.twig' => '?string',
                'home-custom-content.html.twig' => '?string',
                'home-newsletter.html.twig' => '?string',
                'home-posts.html.twig' => '?string',
                'home-socials.html.twig' => '?string',
                'manifesto-list.html.twig' => '?string',
                'manifesto-view.html.twig' => '?string',
                'trombinoscope-list.html.twig' => '?string',
                'trombinoscope-view.html.twig' => '?string',
            ],
            'redirections' => new Property([
                'type' => 'array',
                'items' => new Items([
                    'type' => 'object',
                    'properties' => [
                        new Property(['property' => 'source', 'type' => 'string']),
                        new Property(['property' => 'target', 'type' => 'string']),
                        new Property(['property' => 'code', 'type' => 'integer']),
                    ],
                ]),
            ]),
            'tools' => new Property([
                'type' => 'array',
                'items' => new Items(['type' => 'string', 'enum' => Features::allTools()]),
            ]),
            'access' => [
                'username' => '?string',
                'password' => '?string',
            ],
            'socials' => [
                'email' => '?string',
                'facebook' => '?string',
                'twitter' => '?string',
                'instagram' => '?string',
                'linkedin' => '?string',
                'youtube' => '?string',
                'medium' => '?string',
                'telegram' => '?string',
                'snapchat' => '?string',
            ],
            'socialSharers' => [
                'facebook' => '?string',
                'twitter' => '?string',
                'linkedin' => '?string',
                'telegram' => '?string',
                'whatsapp' => '?string',
                'email' => '?string',
            ],
            'legal' => [
                'name' => '?string',
                'email' => '?string',
                'address' => '?string',
                'publisherName' => '?string',
                'publisherRole' => '?string',
            ],
            'header' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/MenuItem']),
                ]),
            ],
            'footer' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/MenuItem']),
                ]),
            ],
            'home' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/PageBlock']),
                ]),
            ],
        ];
    }

    public function includeHeader(Project $project)
    {
        return $this->primitive(['data' => $this->resolveProjectMenu($project, MenuItem::POSITION_HEADER)]);
    }

    public function includeFooter(Project $project)
    {
        return $this->primitive(['data' => $this->resolveProjectMenu($project, MenuItem::POSITION_FOOTER)]);
    }

    public function includeHome(Project $project)
    {
        return $this->collection($this->blockRepository->getApiBlocks($project, BlockInterface::PAGE_HOME), $this->homeBlockTranformer);
    }

    private function createAssetUrl(Project $project, string $route, array $params = []): string
    {
        return rtrim('//ca.'.$project->getRootDomain()->getName().$this->urlGenerator->generate($route, $params), '/');
    }

    private function resolveProjectMenu(Project $project, string $position): iterable
    {
        if (!isset($this->cache['menu'])) {
            $this->cache['menu'] = $this->menuItemRepository->getApiProjectTree($project);
        }

        return $this->cache['menu'][$position] ?? [];
    }
}
