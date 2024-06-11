<?php

namespace App\DataManager;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Area;
use App\Entity\Community\Tag;
use App\Entity\Model\NotificationSettings;
use App\Entity\Model\SocialSharers;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Theme\WebsiteTheme;
use App\Entity\User;
use App\Entity\Website\Event;
use App\Entity\Website\Form;
use App\Entity\Website\ManifestoTopic;
use App\Entity\Website\MenuItem;
use App\Entity\Website\Page;
use App\Entity\Website\PageBlock;
use App\Entity\Website\Post;
use App\Entity\Website\TrombinoscopePerson;
use App\Form\Admin\Model\StartOnPremiseData;
use App\Form\Appearance\Model\LogosData;
use App\Form\Appearance\Model\WebsiteIntroData;
use App\Form\Project\Model\UpdateMetasData;
use App\Form\Project\Model\UpdateSocialAccountsData;
use App\Mailer\PlatformMailer;
use App\Platform\Features;
use App\Proxy\DomainManager;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Util\Uid;
use App\Website\PageBlock\BlockInterface;
use App\Website\PageBlock\HomeCtaBlock;
use App\Website\PageBlock\HomeNewsletterBlock;
use App\Website\PageBlock\HomePostsBlock;
use App\Website\PageBlock\HomeSocialsBlock;
use App\Website\PageBlockManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ProjectDataManager implements ServiceSubscriberInterface
{
    private string $defaultWebsiteTheme;
    private ContainerInterface $locator;

    public function __construct(string $defaultWebsiteTheme, ContainerInterface $locator)
    {
        $this->defaultWebsiteTheme = $defaultWebsiteTheme;
        $this->locator = $locator;
    }

    public static function getSubscribedServices(): array
    {
        return [
            // Project creation
            EntityManagerInterface::class,
            CdnUploader::class,
            DomainManager::class,
            PageBlockManager::class,
            PlatformMailer::class,

            // Website duplication
            EventDataManager::class,
            FormDataManager::class,
            ManifestoDataManager::class,
            PageDataManager::class,
            PostDataManager::class,
            TrombinoscopeDataManager::class,
        ];
    }

    public function createDefault(Organization $organization, string $name, array $modules, array $tools, array $areas = [], array $tags = [], bool $notify = true): Project
    {
        $project = new Project($organization, $name, $this->getDefaultWebsiteTheme(), 'fr');
        $project->updateModules($modules, $tools);
        $project->applyEmailingLegalitiesUpdate(file_get_contents(__DIR__.'/data/emailing_legalities.html.twig'));

        // Areas
        foreach ($areas as $areaId) {
            if ($areaId && ($area = $this->getEm()->find(Area::class, $areaId))) {
                $project->getAreas()->add($area);
            }
        }

        // Tags
        foreach ($tags as $tagId) {
            if ($tagId && ($tag = $this->getEm()->find(Tag::class, $tagId))) {
                $project->getTags()->add($tag);
                $tag->getProjects()->add($project);
            }
        }

        // Website config
        $project->applyMetasUpdate(new UpdateMetasData($name, 'Réfléchir et agir ensemble !'));
        $project->applySocialSharersUpdate(new SocialSharers([
            SocialSharers::FACEBOOK,
            SocialSharers::TWITTER,
            SocialSharers::TELEGRAM,
            SocialSharers::WHATSAPP,
        ]));

        // Domain
        $project = $this->configureTrialDomain($project);

        // Persist the project
        $this->getEm()->persist($project);
        $this->getEm()->flush();

        // Populate default data
        $pagesRefs = $this->createDefaultPages($project);
        $this->createDefaultMenuItems($pagesRefs, $project);
        $this->createDefaultHomeBlocks($project);

        // Notify
        if ($notify) {
            $this->notifyOrgaAdmins($project);
        }

        return $project;
    }

    public function createOnPremise(Organization $organization, StartOnPremiseData $data)
    {
        $uploader = $this->locator->get(CdnUploader::class);

        $web = new Project($organization, $data->candidateName, $this->getDefaultWebsiteTheme(), 'fr');
        $web->updateModules([Features::MODULE_WEBSITE, Features::MODULE_COMMUNITY], [
            Features::TOOL_WEBSITE_PAGES,
            Features::TOOL_WEBSITE_POSTS,
            Features::TOOL_WEBSITE_DOCUMENTS,
            Features::TOOL_WEBSITE_EVENTS,
            Features::TOOL_WEBSITE_FORMS,
            Features::TOOL_WEBSITE_NEWSLETTER,
            Features::TOOL_WEBSITE_TROMBINOSCOPE,
            Features::TOOL_WEBSITE_MANIFESTO,
            Features::TOOL_COMMUNITY_CONTACTS,
            Features::TOOL_COMMUNITY_EMAILING,
        ]);

        // Website config
        $web->applyMetasUpdate(new UpdateMetasData($data->websiteName, $data->websiteDescription));
        $web->applyWebsiteIntroUpdate(new WebsiteIntroData($data->websiteName, $data->websiteDescription));
        $web->applySocialSharersUpdate(new SocialSharers([
            SocialSharers::FACEBOOK,
            SocialSharers::TWITTER,
            SocialSharers::TELEGRAM,
            SocialSharers::WHATSAPP,
        ]));
        $web->applySocialsAccountsUpdate(new UpdateSocialAccountsData(
            facebook: $data->facebook,
            instagram: $data->instagram,
            twitter: $data->twitter,
            linkedIn: $data->linkedIn,
            youtube: $data->youtube,
            medium: $data->medium,
            telegram: $data->telegram,
            snapchat: $data->snapchat,
        ));

        // Domain
        /** @var DomainManager $domainManager */
        $domainManager = $this->locator->get(DomainManager::class);
        $web->updateDomain($domainManager->createDomain($organization, $data->domain));

        // Persist the project
        $this->getEm()->persist($web);
        $this->getEm()->flush();

        // Uploads
        if ($data->mainImage) {
            $web->setWebsiteMainImage($uploader->upload(
                CdnUploadRequest::createWebsiteHomeMainImageRequest($web, $data->mainImage)
            ));
        }

        if ($data->favicon) {
            $web->setAppearanceIcon($uploader->upload(
                CdnUploadRequest::createProjectIconRequest($web, $data->favicon)
            ));
        }

        if ($data->shareImage) {
            $web->setWebsiteSharer($uploader->upload(
                CdnUploadRequest::createProjectSharerRequest($web, $data->shareImage)
            ));
        }

        $this->getEm()->persist($web);
        $this->getEm()->flush();

        // Populate default data
        $pagesRefs = $this->createDefaultPages($web);
        $this->createDefaultMenuItems($pagesRefs, $web);
        $this->createDefaultHomeBlocks($web);
    }

    public function duplicate(Project $project, bool $notify = true): Project
    {
        $duplicate = $project->duplicate();

        // Uploads
        $uploader = $this->locator->get(CdnUploader::class);

        // Logos
        $logosData = new LogosData();

        if ($project->getAppearanceLogoDark()) {
            $logosData->appearanceLogoDarkUpload = $uploader->duplicate($project->getAppearanceLogoDark());
        }

        if ($project->getAppearanceLogoWhite()) {
            $logosData->appearanceLogoWhiteUpload = $uploader->duplicate($project->getAppearanceLogoWhite());
        }

        if ($project->getAppearanceIcon()) {
            $logosData->appearanceIconUpload = $uploader->duplicate($project->getAppearanceIcon());
        }

        $duplicate->applyLogosUpdate($logosData);

        // Website images
        if ($project->getWebsiteSharer()) {
            $duplicate->setWebsiteSharer($uploader->duplicate($project->getWebsiteSharer()));
        }

        if ($project->getWebsiteMainImage()) {
            $duplicate->setWebsiteMainImage($uploader->duplicate($project->getWebsiteMainImage()));
        }

        if ($project->getWebsiteMainVideo()) {
            $duplicate->setWebsiteMainVideo($uploader->duplicate($project->getWebsiteMainVideo()));
        }

        // Domain
        $duplicate = $this->configureTrialDomain($duplicate);

        // Persist
        $this->getEm()->persist($duplicate);
        $this->getEm()->flush();

        // Duplicate menu
        $items = $this->getEm()->getRepository(MenuItem::class)->findBy(
            ['project' => $project],
            ['parent' => 'DESC'], // Find root items first
        );

        $refs = [];
        foreach ($items as $item) {
            $dupItem = $item->duplicate($item->getParent() ? $refs[$item->getParent()->getId()] : null);
            $dupItem->setProject($duplicate);

            $this->getEm()->persist($dupItem);
            $this->getEm()->flush();

            $refs[$item->getId()] = $dupItem;
        }

        // Duplicate homepage
        $items = $this->getEm()->getRepository(PageBlock::class)->findBy(['project' => $project], ['weight' => 'ASC']);
        foreach ($items as $block) {
            $this->getEm()->persist($block->duplicate($duplicate));
        }

        $this->getEm()->flush();

        // Duplicate website content
        $this->duplicateProjectContent(PageDataManager::class, Page::class, $project, $duplicate);
        $this->duplicateProjectContent(PostDataManager::class, Post::class, $project, $duplicate);
        $this->duplicateProjectContent(TrombinoscopeDataManager::class, TrombinoscopePerson::class, $project, $duplicate);
        $this->duplicateProjectContent(ManifestoDataManager::class, ManifestoTopic::class, $project, $duplicate);
        $this->duplicateProjectContent(EventDataManager::class, Event::class, $project, $duplicate);

        // Duplicate forms without answers
        $dataManager = $this->locator->get(FormDataManager::class);
        $repository = $this->getEm()->getRepository(Form::class);

        foreach ($repository->findBy(['project' => $project]) as $form) {
            if (!$form->getAnswers()->count()) {
                $dataManager->move($dataManager->duplicate($form), $duplicate);
            }
        }

        // Notify
        if ($notify) {
            $this->notifyOrgaAdmins($project);
        }

        return $duplicate;
    }

    public function move(Project $project, Organization $into): Project
    {
        // Populate tags in the new orga
        if ($project->getTags()->count()) {
            $intoOrgaTags = [];
            foreach ($this->getEm()->getRepository(Tag::class)->findBy(['organization' => $into]) as $tag) {
                $intoOrgaTags[$tag->getName()] = $tag;
            }

            $tagsIds = [];
            foreach ($project->getTags() as $pt) {
                if ($intoOrgaTag = $intoOrgaTags[$pt->getName()] ?? null) {
                    $tagsIds[] = $intoOrgaTag->getId();
                } else {
                    $this->getEm()->persist($t = new Tag($into, $pt->getName()));
                    $this->getEm()->flush();

                    $tagsIds[] = $t->getId();
                }
            }

            $this->getEm()->getRepository(ProjectRepository::class)->updateTags($project, $tagsIds);
        }

        // Change orga
        $project->setOrganization($into);

        $this->getEm()->persist($project);
        $this->getEm()->flush();

        return $project;
    }

    private function configureTrialDomain(Project $project): Project
    {
        $domainManager = $this->locator->get(DomainManager::class);

        $project->updateDomain(
            $domainManager->getTrialDomain(),
            $trialSubdomain = $domainManager->generateTrialSubdomain($project)
        );

        // Connect the trial subdomain to the infrastructure
        $domainManager->connectTrialSubdomain($trialSubdomain);

        return $project;
    }

    private function duplicateProjectContent(string $dataManagerClass, string $entityClass, Project $from, Project $to)
    {
        $dataManager = $this->locator->get($dataManagerClass);
        $repository = $this->getEm()->getRepository($entityClass);

        foreach ($repository->findBy(['project' => $from]) as $entity) {
            $dataManager->move($dataManager->duplicate($entity, true), $to);
        }
    }

    private function createDefaultPages(Project $project): array
    {
        $items = [
            'privacy' => [
                'title' => 'Données personnelles',
                'content' => file_get_contents(__DIR__.'/data/privacy.html'),
            ],
            'terms' => [
                'title' => 'Conditions d\'utilisation',
                'content' => file_get_contents(__DIR__.'/data/tos.html'),
            ],
        ];

        $references = [];
        foreach ($items as $ref => $data) {
            $this->getEm()->persist($references[$ref] = Page::createDefaultPage($project, $data['title'], $data['content']));
        }

        $this->getEm()->flush();

        return $references;
    }

    private function createDefaultMenuItems(array $pagesReferences, Project $project)
    {
        $menus = [
            'header' => [
                'Notre équipe' => '/trombinoscope',
                'Nos propositions' => '/manifesto',
                'Actualités' => '/posts',
                'Événements' => '/events',
            ],
            'footer' => [
                'Mentions légales' => '/legal',
                'Données personnelles' => $this->createPageUrl($pagesReferences, 'privacy'),
                'Conditions d\'utilisation' => $this->createPageUrl($pagesReferences, 'terms'),
            ],
        ];

        $i = 1;
        foreach ($menus as $position => $items) {
            foreach ($items as $name => $url) {
                $this->getEm()->persist(new MenuItem($project, $position, $name, $url, $i));
                ++$i;
            }
        }

        $this->getEm()->flush();
    }

    private function createDefaultHomeBlocks(Project $project)
    {
        $items = [
            1 => HomeCtaBlock::TYPE,
            2 => HomePostsBlock::TYPE,
            3 => HomeNewsletterBlock::TYPE,
            4 => HomeSocialsBlock::TYPE,
        ];

        foreach ($items as $weight => $type) {
            $block = $this->locator->get(PageBlockManager::class)->createBlock(
                $project,
                BlockInterface::PAGE_HOME,
                $type
            );
            $block->setWeight($weight);

            $this->getEm()->persist($block);
        }

        $this->getEm()->flush();
    }

    private function createPageUrl(array $pagesRefs, string $refName): string
    {
        $page = $pagesRefs[$refName];

        return '/pages/'.Uid::toBase62($page->getUuid()).'/'.$page->getSlug();
    }

    private function notifyOrgaAdmins(Project $project)
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getEm()->getRepository(User::class);

        foreach ($userRepo->findOrganizationMembers($project->getOrganization(), true) as $member) {
            // Notify only administrators who wants to be notified
            if (!$member->getNotificationSettings()->isEnabled(NotificationSettings::EVENT_PROJECT_CREATED)) {
                continue;
            }

            $this->locator->get(PlatformMailer::class)->sendNotificationNewProject(
                $member,
                $project->getOrganization(),
                $project
            );
        }
    }

    private function getDefaultWebsiteTheme(): ?WebsiteTheme
    {
        $defaultWebsiteTheme = $this->getEm()->getRepository(WebsiteTheme::class)->findOneBy([
            'repositoryFullName' => $this->defaultWebsiteTheme,
        ]);

        if (!$defaultWebsiteTheme) {
            throw new \LogicException('No default website theme available.');
        }

        return $defaultWebsiteTheme;
    }

    private function getEm(): EntityManagerInterface
    {
        return $this->locator->get(EntityManagerInterface::class);
    }
}
