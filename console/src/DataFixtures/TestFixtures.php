<?php

namespace App\DataFixtures;

use App\Billing\Model\OrderLine;
use App\DataFixtures\Data\Events;
use App\DataFixtures\Data\PageViews;
use App\DataFixtures\Data\Sessions;
use App\Entity\Announcement;
use App\Entity\Area;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Entity\Community\Ambiguity;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\EmailAutomationMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\EmailingCampaignMessage;
use App\Entity\Community\Import;
use App\Entity\Community\Model\ImportHead;
use App\Entity\Community\PhoningCampaign;
use App\Entity\Community\PhoningCampaignCall;
use App\Entity\Community\PhoningCampaignTarget;
use App\Entity\Community\Tag;
use App\Entity\Community\TextingCampaign;
use App\Entity\Community\TextingCampaignMessage;
use App\Entity\Domain;
use App\Entity\Integration\IntegromatWebhook;
use App\Entity\Integration\RevueAccount;
use App\Entity\Integration\TelegramApp;
use App\Entity\Integration\TelegramAppAuthorization;
use App\Entity\Model\CloudflareDomainConfig;
use App\Entity\Model\PartnerMenu;
use App\Entity\Model\PartnerMenuItem;
use App\Entity\Model\SocialSharers;
use App\Entity\Model\SubscriptionNotifications;
use App\Entity\Organization;
use App\Entity\OrganizationMainTag;
use App\Entity\OrganizationMember;
use App\Entity\Platform\Job;
use App\Entity\Project;
use App\Entity\Registration;
use App\Entity\Theme\ProjectAsset;
use App\Entity\Theme\WebsiteTheme;
use App\Entity\Theme\WebsiteThemeAsset;
use App\Entity\Upload;
use App\Entity\User;
use App\Entity\Website\Document;
use App\Entity\Website\Event;
use App\Entity\Website\EventCategory;
use App\Entity\Website\Form;
use App\Entity\Website\FormAnswer;
use App\Entity\Website\FormBlock;
use App\Entity\Website\ManifestoProposal;
use App\Entity\Website\ManifestoTopic;
use App\Entity\Website\MenuItem;
use App\Entity\Website\Page;
use App\Entity\Website\PageBlock;
use App\Entity\Website\PageCategory;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Entity\Website\Redirection;
use App\Entity\Website\TrombinoscopeCategory;
use App\Entity\Website\TrombinoscopePerson;
use App\Platform\Permissions;
use App\Platform\Plans;
use App\Util\Json;
use App\Website\PageBlock\BlockInterface;
use App\Website\PageBlock\HomeContentBlock;
use App\Website\PageBlock\HomeCtaBlock;
use App\Website\PageBlock\HomeEventsBlock;
use App\Website\PageBlock\HomeNewsletterBlock;
use App\Website\PageBlock\HomePostsBlock;
use App\Website\PageBlock\HomeSocialsBlock;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TestFixtures extends AbstractFixtures
{
    protected static int $order = 1;
    protected static array $groups = ['test'];

    /** @var Area[] */
    private array $areas = [];

    /** @var User[] */
    private array $users = [];

    /** @var Organization[] */
    private array $orgas = [];

    /** @var Order[] */
    private array $orders = [];

    /** @var TelegramApp[] */
    private array $telegramApps = [];

    /** @var WebsiteTheme[] */
    private array $websiteThemes = [];

    /** @var Domain[] */
    private array $domains = [];

    /** @var Project[] */
    private array $projects = [];

    /** @var Tag[] */
    private array $tags = [];

    /** @var Upload[] */
    private array $uploads = [];

    /** @var PageCategory[] */
    private array $pageCategories = [];

    /** @var PostCategory[] */
    private array $postCategories = [];

    /** @var EventCategory[] */
    private array $eventCategories = [];

    /** @var TrombinoscopeCategory[] */
    private array $trombinoscopeCategories = [];

    /** @var ManifestoTopic[] */
    private array $manifestoTopics = [];

    /** @var Form[] */
    private array $forms = [];

    /** @var Contact[] */
    private array $contacts = [];

    /** @var Import[] */
    private array $imports = [];

    /** @var EmailingCampaign[] */
    private array $emailingCampaigns = [];

    /** @var TextingCampaign[] */
    private array $textingCampaigns = [];

    /** @var PhoningCampaign[] */
    private array $phoningCampaigns = [];

    /** @var PhoningCampaignTarget[] */
    private array $phoningCampaignsTargets = [];

    /** @var EmailAutomation[] */
    private array $emailAutomations = [];

    public function doLoad()
    {
        $this->loadAnnouncements();
        $this->loadPlatformJobs();
        $this->loadAreas();
        $this->loadUsers();
        $this->loadOrganizations();
        $this->loadTelegramApps();
        $this->loadTelegramAppsAuthorizations();
        $this->loadIntegromatWebhooks();
        $this->loadRevueAccounts();
        $this->loadRegistrations();
        $this->loadDomains();
        $this->loadTags();
        $this->loadWebsiteThemes();
        $this->loadProjects();
        $this->loadRedirections();
        $this->loadUploads();
        $this->loadOrders();
        $this->loadQuotes();
        $this->loadWebsiteThemesAssets();
        $this->loadProjectAssets();
        $this->loadDocuments();
        $this->loadMenuItems();
        $this->loadPageCategories();
        $this->loadPages();
        $this->loadPostCategories();
        $this->loadPosts();
        $this->loadEventsCategories();
        $this->loadForms();
        $this->loadEvents();
        $this->loadTrombinoscopeCategories();
        $this->loadTrombinoscopePersons();
        $this->loadManifestoTopics();
        $this->loadManifestoProposals();
        $this->loadHomeBlocks();
        $this->loadContacts();
        $this->loadContactsAmbiguous();
        $this->loadImports();
        $this->loadFormsBlocks();
        $this->loadFormsAnswers();
        $this->loadEmailingCampaigns();
        $this->loadEmailingCampaignsMessages();
        $this->loadTextingCampaigns();
        $this->loadTextingCampaignsMessages();
        $this->loadPhoningCampaigns();
        $this->loadPhoningCampaignsTargets();
        $this->loadPhoningCampaignsCalls();
        $this->loadEmailAutomations();
        $this->loadEmailAutomationsMessages();
        $this->loadAnalyticsPageViews();
        $this->loadAnalyticsSessions();
        $this->loadAnalyticsEvents();
        $this->loadAnalyticsContactsCreations();
    }

    private function loadAnnouncements()
    {
        $items = [
            [
                'title' => 'Améliorations de la gestion des images',
                'description' => 'L\'envoi d\'image dans le module Candidats a été amélioré pour éviter le mauvais affichage des portraits. N\'hésitez pas à renvoyer vos images si elles ne sont pas parfaitement affichées !',
                'linkText' => 'Vérifier mes images de candidats',
                'linkUrl' => '/candidates',
                'date' => new \DateTime('2 days ago'),
            ],
            [
                'title' => 'Mettez en avant votre programe',
                'description' => 'Présentez de propositions concrètes pour votre ville en les organisant en thèmes et en leur associant une couleur et une image.',
                'date' => new \DateTime('3 days ago'),
            ],
            [
                'title' => 'Cachez la position des candidats sur votre liste',
                'description' => 'Après de nombreuses demandes, nous venons d\'ajouter la possibilité sur le module Candidats de cacher la position de chacun sur votre liste!',
                'date' => new \DateTime('4 days ago'),
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(Announcement::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadPlatformJobs()
    {
        $job = new Job('add_tag_batch', 0, 10);
        $job->finish(['key' => 'value']);

        $this->em->persist($job);
        $this->em->flush();
    }

    private function loadAreas()
    {
        $items = [
            36778547219895752 => [
                'type' => Area::TYPE_COUNTRY,
                'code' => 'fr',
                'name' => 'France',
            ],
            64795327863947811 => [
                'type' => Area::TYPE_PROVINCE,
                'code' => 'fr_11',
                'name' => 'Île-de-France',
                'parent' => 36778547219895752,
            ],
            65636974309722332 => [
                'type' => Area::TYPE_DISTRICT,
                'code' => 'fr_11_92',
                'name' => 'Hauts-de-Seine',
                'parent' => 64795327863947811,
            ],
            3066627596119454 => [
                'type' => Area::TYPE_COMMUNITY,
                'code' => 'fr_11_92_922',
                'name' => 'Arrondissement de Nanterre',
                'parent' => 65636974309722332,
            ],
            39389989938296926 => [
                'type' => Area::TYPE_ZIP_CODE,
                'code' => 'fr_11_92_922_92110',
                'name' => '92110',
                'description' => 'Clichy',
                'parent' => 3066627596119454,
            ],
            23145772964146041 => [
                'type' => Area::TYPE_COUNTRY,
                'code' => 'ad',
                'name' => 'Andorra',
            ],
            22288902908228028 => [
                'type' => Area::TYPE_ZIP_CODE,
                'code' => 'ad_92110',
                'name' => '92110',
                'description' => 'Test conflict usage of country',
                'parent' => 23145772964146041,
            ],
            35199436697483610 => [
                'type' => Area::TYPE_COUNTRY,
                'code' => 'at',
                'name' => 'Austria',
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->areas[$id] = Area::createFixture(array_merge($data, [
                'id' => $id,
                'parent' => isset($data['parent']) ? $this->areas[$data['parent']] : null,
            ])));
        }

        $this->em->flush();
    }

    private function loadUsers()
    {
        $items = [
            '17ef21d1-27ad-46a6-a2fe-95e5e725473c' => [
                'email' => 'titouan.galopin@citipo.com',
                'firstName' => 'Titouan',
                'lastName' => 'Galopin',
                'isAdmin' => true,
            ],
            '115fe4bc-55f4-4c1f-a0da-db96e380a839' => [
                'email' => 'adrien.duguet@citipo.com',
                'firstName' => 'Adrien',
                'lastName' => 'Duguet',
                'isPartner' => true,
                'partnerName' => 'Gestion locale',
                'partnerMenu' => (
                    new PartnerMenu([
                        new PartnerMenuItem('Legal hotline', 'https://citipo.com'),
                        new PartnerMenuItem('Accounting hotline', 'https://citipo.com'),
                    ])
                )->toArray(),
            ],
            'ae706aa0-55a0-463f-9245-6cf65ae411c1' => [
                'email' => 'ema.anderson@away.com',
                'firstName' => 'Ema',
                'lastName' => 'Anderson',
            ],
            '4e293056-543a-46a0-b625-0de934243ac2' => [
                'email' => 'arianneverreau@example.com',
                'firstName' => 'Arianne',
                'lastName' => 'Verreau',
            ],
        ];

        foreach ($items as $id => $data) {
            $user = User::createFixture(array_merge(['uuid' => $id], $data));
            $user->changePassword($this->hasher->hashPassword($user, 'password'));

            $this->em->persist($this->users[$id] = $user);
        }

        $this->em->flush();
    }

    private function loadOrganizations()
    {
        $items = [
            // Citipo (active)
            '219025aa-7fe2-4385-ad8f-31f386720d10' => [
                'name' => 'Citipo',
                'owner' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'],
                'quorumToken' => 'quorum_token',
                'quorumDefaultCity' => 'paris',
                'apiToken' => '645b5d9c2e3ac8064c540b276b6c180692582868cd21c6b50e4442267e5a341f',
                'textingSenderCode' => 'CPGT',
                'billingPricePerMonth' => 13900,
                'currentPeriodEnd' => new \DateTime('+19 days'),
                'subscriptionNotifications' => (new SubscriptionNotifications([]))
                    // 30 days expiration notification
                    ->withMarkedNotified(SubscriptionNotifications::TYPE_EXPIRATION, new \DateTime('-11 days')),
            ],

            // Example (trial)
            '682746ea-3e2f-4e5b-983b-6548258a2033' => [
                'name' => 'Example Co',
                'trialing' => true,
                'currentPeriodEnd' => new \DateTime('2032-05-08'),
                'owner' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'],
                'credits' => 0,
                'textsCredits' => 0,
            ],

            // Acme (active)
            'cbeb774c-284c-43e3-923a-5a2388340f91' => [
                'name' => 'Acme',
                'partner' => $this->users['115fe4bc-55f4-4c1f-a0da-db96e380a839'], // Adrien Duguet
                'currentPeriodEnd' => new \DateTime('2030-05-08'),
                'owner' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'],
                'quorumToken' => 'quorum_token',
                'quorumDefaultCity' => 'paris',
                'projectsSlots' => 1,
                'credits' => 5000,
                'billingPricePerMonth' => 13900,
                'billingEmail' => 'billing@citipo.com',
                'mollieCustomerId' => 'cst_DKnSArGRCm',
            ],

            // Incomplete (incomplete subscription to test webhook sync)
            'acbcd975-6c11-49dc-9d60-a39a5d071c04' => [
                'name' => 'Incomplete',
                'incomplete' => true,
                'projectsSlots' => 0,
                'credits' => 0,
                'owner' => $this->users['4e293056-543a-46a0-b625-0de934243ac2'],
            ],

            // Expired trial
            '941ab5a3-2af6-4405-84ba-a1d2ad80292b' => [
                'name' => 'Expired',
                'trialing' => true,
                'currentPeriodEnd' => new \DateTime('-1 month'),
                'owner' => $this->users['4e293056-543a-46a0-b625-0de934243ac2'],
            ],

            // Essential plan (active)
            'eafd4a15-7812-4468-aae1-d11217667be0' => [
                'name' => 'Essential',
                'owner' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'],
                'billingPricePerMonth' => 1900,
                'subscriptionPlan' => Plans::ESSENTIAL,
            ],

            // Standard plan (active)
            '307c3c05-1873-4e81-ae7d-a1644fa8c5a7' => [
                'name' => 'Standard',
                'owner' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'],
                'billingPricePerMonth' => 3900,
                'subscriptionPlan' => Plans::STANDARD,
            ],

            // Premium plan (active)
            'a54ee91a-1c37-48a1-a75d-119ac8ac798e' => [
                'name' => 'Premium',
                'owner' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'],
                'billingPricePerMonth' => 7900,
                'subscriptionPlan' => Plans::PREMIUM,
                'showPreview' => false,
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($orga = Organization::createFixture(array_merge($data, ['uuid' => $id])));
            $this->orgas[$id] = $orga;

            $this->em->persist(OrganizationMember::createFixture([
                'orga' => $orga,
                'user' => $data['owner'],
                'labels' => [$orga->getName()],
            ]));

            if ($data['partner'] ?? null) {
                $this->em->persist(OrganizationMember::createFixture([
                    'orga' => $orga,
                    'user' => $data['partner'],
                    'labels' => [$orga->getName()],
                ]));
            }

            foreach ($data['collaborators'] ?? [] as $collaborator) {
                $this->em->persist(OrganizationMember::createFixture([
                    'orga' => $orga,
                    'user' => $collaborator,
                    'labels' => [$orga->getName()],
                ]));
            }
        }

        $this->em->flush();
    }

    private function loadOrders()
    {
        $items = [
            // Draft emails
            '35bc37bc-8e8a-4d37-b5c5-9ec5a62eee28' => [
                'company' => 'citipo',
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'mollieId' => 'ord_35bc37bc',
                'action' => OrderAction::addEmailCredits(1000),
                'recipient' => new OrderRecipient('Titouan', 'Galopin', 'titouan.galopin@citipo.com', 'en'),
                'amount' => 1800,
                'lines' => [
                    OrderLine::fromArray([
                        'type' => 'digital',
                        'name' => 'Crédit email',
                        'quantity' => 1000,
                        'unitPrice' => 0.003,
                        'vatRate' => 20.0,
                    ]),
                ],
            ],

            // Draft texts
            '7698f242-0a05-496c-b542-34236e9de12c' => [
                'company' => 'citipo',
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'mollieId' => 'ord_7698f242',
                'action' => OrderAction::addTextCredits(150),
                'recipient' => new OrderRecipient('Titouan', 'Galopin', 'titouan.galopin@citipo.com', 'en'),
                'amount' => 1800,
            ],

            // Expired
            'bdd21e4d-93f5-4c1c-97fd-7ba730ee4394' => [
                'company' => 'citipo',
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'mollieId' => 'ord_bdd21e4d',
                'action' => OrderAction::addEmailCredits(1000),
                'recipient' => new OrderRecipient('Titouan', 'Galopin', 'titouan.galopin@citipo.com', 'en'),
                'amount' => 1800,
            ],

            // Invoiced
            'b1e80c11-ca03-4e11-858a-dc00b05c5527' => [
                'company' => 'citipo',
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'mollieId' => 'ord_b1e80c11',
                'action' => OrderAction::addEmailCredits(2000),
                'recipient' => new OrderRecipient('Titouan', 'Galopin', 'titouan.galopin@citipo.com', 'en'),
                'amount' => 720,
                'paidAt' => new \DateTime('2021-12-28 18:44:12'),
                'invoiceNumber' => 156,
                'invoicePdf' => $this->uploads['invoice.pdf'],
                'invoiceSentAt' => new \DateTime('2021-12-28 18:45:34'),
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->orders[$id] = Order::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadQuotes()
    {
        $items = [
            'c5f0ebfa-625c-4b57-9d2c-6e643fe1d973' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'company' => 'citipo',
                'recipient' => new OrderRecipient('Titouan', 'Galopin', 'titouan.galopin@citipo.com', 'en'),
                'amount' => 1800,
                'lines' => [
                    OrderLine::fromArray([
                        'type' => 'digital',
                        'name' => 'Crédit email',
                        'quantity' => 1000,
                        'unitPrice' => 0.003,
                        'vatRate' => 20.0,
                    ]),
                ],
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(Quote::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadTelegramApps()
    {
        $items = [
            '3a9c0c55-bb74-48d7-9cce-117fbf8e0293' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'username' => 'citipodebugbot',
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(
                $this->telegramApps[$id] = TelegramApp::createFixture(array_merge($data, ['uuid' => $id]))
            );
        }

        $this->em->flush();
    }

    private function loadTelegramAppsAuthorizations()
    {
        $items = [
            'f2cd1ba7-4f92-4d16-83e8-89534d943758' => [
                'app' => $this->telegramApps['3a9c0c55-bb74-48d7-9cce-117fbf8e0293'], // citipodebugbot
                'member' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'], // titouan.galopin@citipo.com
                'apiToken' => 'telegram_16c43545f60e99a58c699e8473266352b6c0dfdd36c5963883ea3e7a80662538',
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(TelegramAppAuthorization::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadIntegromatWebhooks()
    {
        $items = [
            [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'], // Citipo
                'url' => 'https://hook.integromat.com/swme2wu7n735qcmhbeyfj595c2ey2aju',
                'token' => 'adfdb95e90476cf7628eb8cd5c739cde6307b0e505daa60e2541c245adab86ef',
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(IntegromatWebhook::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadRevueAccounts()
    {
        $items = [
            'deae46c2-20df-4ba3-9c08-9bfc1d638f32' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'label' => 'citipoapp',
                'apiToken' => 'adfdb95e90476cf7628eb8cd5c739cde',
            ],
            '4972c1e9-669e-485d-b225-1104ae3ce35a' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'], // Acme
                'label' => 'titouangalopin',
                'apiToken' => '6307b0e505daa60e2541c245adab86ef',
                'lastSync' => new \DateTime('2021-09-26 11:30'),
                'enabled' => false,
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(RevueAccount::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadRegistrations()
    {
        $items = [
            ['email' => 'noone@citipo.com', 'isAdmin' => true, 'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            ['email' => 'contact@email.com', 'isAdmin' => true, 'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            ['email' => 'hello@moto.com', 'isAdmin' => true, 'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            [
                'email' => 'verylongemailaddressthatshouldbetruncated@gmail.com',
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'permissions' => [
                    '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [Permissions::WEBSITE_PAGES_MANAGE => true],
                    'e816bcc6-0568-46d1-b0c5-917ce4810a87' => [Permissions::WEBSITE_PAGES_MANAGE => true],
                ],
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(Registration::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadDomains()
    {
        $items = [
            'citipo.com' => ['orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            'c4o.io' => ['orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            'localhost' => ['orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            'example.com' => ['orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
            'exampleco.com' => ['orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10']],
        ];

        foreach ($items as $name => $data) {
            $this->domains[$name] = new Domain($data['orga'], $name);
            $this->domains[$name]->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_ready' => 1, 'postmark_ready' => 1]);
            $this->domains[$name]->setCloudflareConfig(new CloudflareDomainConfig(md5($name), $name, 'active', []));

            $this->em->persist($this->domains[$name]);
        }

        $this->em->flush();
    }

    private function loadProjects()
    {
        $items = [
            // Global
            'e816bcc6-0568-46d1-b0c5-917ce4810a87' => [
                'name' => 'Citipo',
                'apiToken' => '748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad',
                'domain' => $this->domains['citipo.com'],
                'websiteLocale' => 'en',
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'socialEmail' => 'idf@citipo.com',
                'socialFacebook' => 'https://facebook.com/project-idf',
                'socialTwitter' => 'https://twitter.com/citipoapp',
                'socialTelegram' => 'citipo_idf',
                'emailingLegalities' => file_get_contents(__DIR__.'/../DataManager/data/emailing_legalities.html.twig'),
            ],

            // Local
            '151f1340-9ad6-47c7-a8a5-838ff955eae7' => [
                'name' => 'Île-de-France',
                'area' => $this->areas[64795327863947811],
                'apiToken' => '3a4683898cdd75936c94475d55049c07c407b64f18e23d6f726894fc0cc79f4f',
                'domain' => $this->domains['citipo.com'],
                'subdomain' => 'ile-de-france',
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'emailingLegalities' => file_get_contents(__DIR__.'/../DataManager/data/emailing_legalities.html.twig'),
            ],

            // Thematic
            '062d7a3b-7cf3-48b0-b905-21f09844fb81' => [
                'name' => 'ExampleTag',
                'tags' => [$this->tags['exampletag'], $this->tags['startwithtag']],
                'apiToken' => '07c407b64f18e23d6f726894fc0cc79f4f3a4683898cdd75936c94475d55049c',
                'domain' => $this->domains['citipo.com'],
                'subdomain' => 'example-tag',
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'emailingLegalities' => file_get_contents(__DIR__.'/../DataManager/data/emailing_legalities.html.twig'),
            ],

            // Trial
            '62241741-7504-4cb9-9d56-a417a3d07bb3' => [
                'name' => 'Trial',
                'domain' => $this->domains['c4o.io'],
                'subdomain' => 'trial-62241741',
                'apiToken' => 'ed17609e4cbcfc2af23df24a996a410ec197d7877006b4d382275b7b63a0a713',
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'emailingLegalities' => file_get_contents(__DIR__.'/../DataManager/data/emailing_legalities.html.twig'),
            ],

            // Acme single project
            '2c720420-65fd-4360-9d77-731758008497' => [
                'name' => 'Acme Inc',
                'domain' => $this->domains['localhost'],
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'apiToken' => '31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a',
                'socialSharers' => [SocialSharers::FACEBOOK, SocialSharers::TWITTER, SocialSharers::TELEGRAM, SocialSharers::WHATSAPP],
                'legalGdprName' => 'Acme Inc SAS',
                'legalGdprEmail' => 'gdpremail@example.com',
                'legalGdprAddress' => 'Postal address',
                'legalPublisherName' => 'Publisher Full Name',
                'legalPublisherRole' => 'Publisher Role',
                'membershipMainPage' => 'Hello world',
                'emailingLegalities' => file_get_contents(__DIR__.'/../DataManager/data/emailing_legalities.html.twig'),
            ],

            // Expired
            '5767c01d-e6c1-4a29-a1d3-194ccd14a93f' => [
                'name' => 'Expired',
                'domain' => $this->domains['example.com'],
                'orga' => $this->orgas['941ab5a3-2af6-4405-84ba-a1d2ad80292b'],
            ],

            // Example Co
            '643e47ea-fd9d-4963-958f-05970de2f88b' => [
                'name' => 'Example Co',
                'domain' => $this->domains['exampleco.com'],
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'apiToken' => '41d7821176ed9079640650922e1290aba97b949362339a7ed5539f0d5b9f21ba',
                'adminApiToken' => 'admin_6d06eec96e8c615b76ccf3b9166b174b4e2f59804f8b97773532957b4acf8691',
                'socialEmail' => 'contact@exampleco.com',
                'socialFacebook' => 'https://facebook.com/exampleco',
                'socialSnapchat' => 'exampleco',
                'socialSharers' => [SocialSharers::FACEBOOK],
            ],

            // Essential
            '4c8d5792-79de-4408-b091-88dbd7c9232b' => [
                'name' => 'Essential',
                'domain' => $this->domains['example.com'],
                'orga' => $this->orgas['eafd4a15-7812-4468-aae1-d11217667be0'],
            ],

            // Standard
            '946a3832-e80d-4075-ae79-9e375965220b' => [
                'name' => 'Standard',
                'domain' => $this->domains['example.com'],
                'orga' => $this->orgas['307c3c05-1873-4e81-ae7d-a1644fa8c5a7'],
            ],

            // Premium
            '272879f0-3c42-457c-bedb-0b4391d9055b' => [
                'name' => 'Premium',
                'domain' => $this->domains['example.com'],
                'apiToken' => 'ccda49719e86f63830e012e5605e4d4354a32c57a219d2540434c673d0f2d1c6',
                'orga' => $this->orgas['a54ee91a-1c37-48a1-a75d-119ac8ac798e'],
            ],
        ];

        foreach ($items as $id => $data) {
            if (!isset($data['theme'])) {
                $data['theme'] = $this->websiteThemes['d325bbff-70bf-40a5-ac25-c0259c0aa126'];
            }

            $this->em->persist($this->projects[$id] = Project::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadUploads()
    {
        $items = [
            'post-image.png' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'page-image.png' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'event-image.png' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'manifesto-image.png' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'file1.pdf' => ['project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87']],
            'file2.pdf' => ['project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87']],
            'les-couts-de-la-campagne.pdf' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'programme.pdf' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'only-for-members.pdf' => ['project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b']],
            'asset.png' => ['project' => $this->projects['2c720420-65fd-4360-9d77-731758008497']],
            'theme-asset.png' => ['project' => $this->projects['2c720420-65fd-4360-9d77-731758008497']],
            'import-not-started.xlsx' => [],
            'import-started.xlsx' => [],
            'import-started-2.xlsx' => [],
            'contact-picture.jpg' => [],
            'invoice.pdf' => [],
        ];

        foreach ($items as $name => $data) {
            $this->em->persist($this->uploads[$name] = Upload::createFixture(array_merge($data, ['name' => $name])));
        }

        $this->em->flush();
    }

    private function loadRedirections()
    {
        $items = [
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'source' => '/redirection/static',
                'target' => '/redirection-2-target',
                'code' => 302,
                'weight' => 2,
            ],
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'source' => '/redirection/dynamic/*/foo',
                'target' => '/redirection/$1/1-target',
                'code' => 301,
                'weight' => 1,
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(Redirection::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadWebsiteThemes()
    {
        $items = [
            'd325bbff-70bf-40a5-ac25-c0259c0aa126' => [
                'author' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'], // Titouan Galopin
                'installationId' => '20980257',
                'repositoryNodeId' => 'R_kgDOGcPqGw',
                'repositoryFullName' => 'citipo/theme-bold',
                'name' => ['fr' => 'Bold', 'en' => 'Bold'],
                'description' => ['fr' => 'Bold Description', 'en' => 'Bold Description'],
                'templates' => [
                    'style' => file_get_contents(__DIR__.'/Resources/theme/style.css.twig'),
                    'script' => file_get_contents(__DIR__.'/Resources/theme/script.js.twig'),
                    'head' => file_get_contents(__DIR__.'/Resources/theme/head.html.twig'),
                    'layout' => file_get_contents(__DIR__.'/Resources/theme/layout.html.twig'),
                    'header' => file_get_contents(__DIR__.'/Resources/theme/header.html.twig'),
                    'footer' => file_get_contents(__DIR__.'/Resources/theme/footer.html.twig'),
                    'list' => file_get_contents(__DIR__.'/Resources/theme/list.html.twig'),
                    'content' => file_get_contents(__DIR__.'/Resources/theme/content.html.twig'),
                    'home' => file_get_contents(__DIR__.'/Resources/theme/home.html.twig'),
                    'home-calls-to-action' => file_get_contents(__DIR__.'/Resources/theme/home/calls-to-action.html.twig'),
                    'home-custom-content' => file_get_contents(__DIR__.'/Resources/theme/home/custom-content.html.twig'),
                    'home-newsletter' => file_get_contents(__DIR__.'/Resources/theme/home/newsletter.html.twig'),
                    'home-posts' => file_get_contents(__DIR__.'/Resources/theme/home/posts.html.twig'),
                    'home-events' => file_get_contents(__DIR__.'/Resources/theme/home/events.html.twig'),
                    'home-socials' => file_get_contents(__DIR__.'/Resources/theme/home/socials.html.twig'),
                    'manifesto-list' => file_get_contents(__DIR__.'/Resources/theme/manifesto/list.html.twig'),
                    'manifesto-view' => file_get_contents(__DIR__.'/Resources/theme/manifesto/view.html.twig'),
                    'trombinoscope-list' => file_get_contents(__DIR__.'/Resources/theme/trombinoscope/list.html.twig'),
                    'trombinoscope-view' => file_get_contents(__DIR__.'/Resources/theme/trombinoscope/view.html.twig'),
                ],
            ],
            '95c22208-9f69-4d8a-a1e0-52de0bafc9d0' => [
                'author' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'], // Titouan Galopin
                'installationId' => '20980257',
                'repositoryNodeId' => 'MDEwOlJlcG9zaXRvcnkzNDc0MDE2OTY=',
                'repositoryFullName' => 'citipo/theme-structured',
                'name' => ['fr' => 'Structured', 'en' => 'Structured'],
            ],
            '17f4e395-ee59-43c1-bf01-f73a0dddcac8' => [
                'author' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'], // Titouan Galopin
                'installationId' => '20980257',
                'repositoryNodeId' => 'f73a0dddcac8',
                'repositoryFullName' => 'citipo/theme-efficient',
                'name' => ['fr' => 'Efficient', 'en' => 'Efficient'],
                'defaultColors' => ['primary' => '000', 'secondary' => '111', 'third' => '222'],
                'defaultFonts' => ['title' => 'Roboto Slab', 'text' => 'Roboto'],
            ],
            '7780b284-2fa8-41f0-bbfd-03f9fa9c4743' => [
                'author' => $this->users['17ef21d1-27ad-46a6-a2fe-95e5e725473c'], // Titouan Galopin
                'installationId' => null,
                'repositoryNodeId' => null,
                'repositoryFullName' => null,
                'name' => ['fr' => 'Archived', 'en' => 'Archived'],
            ],
            'de4b89a0-8d76-48c7-ad37-57631c1457de' => [
                'installationId' => '20980258',
                'repositoryNodeId' => 'zaXRvcnkzNDE',
                'repositoryFullName' => 'citipo/unlinked',
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->websiteThemes[$id] = WebsiteTheme::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadWebsiteThemesAssets()
    {
        $items = [
            'd944daa5-0dda-472e-9585-d053807edcf5' => [
                'file' => $this->uploads['theme-asset.png'],
                'theme' => $this->websiteThemes['d325bbff-70bf-40a5-ac25-c0259c0aa126'],
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(WebsiteThemeAsset::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadProjectAssets()
    {
        $items = [
            'b13e4688-705d-47a3-acac-599ff5846d5f' => [
                'file' => $this->uploads['asset.png'],
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(ProjectAsset::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadDocuments()
    {
        $items = [
            '58da29fd-2190-41c4-8bcb-9e0bbd0ee042' => [
                'file' => $this->uploads['file1.pdf'],
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
            ],
            'fb9cdea0-fc9f-4154-afa6-4010f6753f7c' => [
                'file' => $this->uploads['file2.pdf'],
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'createdAt' => (new \DateTime())->add(new \DateInterval('PT60S')),
            ],
            'e87f6abd-0270-4611-8ffb-0169cc3bbe6a' => [
                'file' => $this->uploads['les-couts-de-la-campagne.pdf'],
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            '7448626f-4a84-4766-b792-eae01a713669' => [
                'file' => $this->uploads['programme.pdf'],
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'createdAt' => new \DateTime('+2 days'),
            ],
            '958cb9f1-d054-4c75-b3b2-a527c806c4c9' => [
                'file' => $this->uploads['only-for-members.pdf'],
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'onlyForMembers' => true,
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(Document::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadMenuItems()
    {
        $items = [
            'header' => [
                'home' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Home',
                    'url' => '/',
                    'weight' => 0,
                ],
                'your-cadidate' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Your candidate',
                    'url' => '/page/your-candidate',
                    'weight' => 1,
                ],
                'biography' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'parent' => 'your-cadidate',
                    'label' => 'Biography',
                    'url' => '/page/biography',
                    'weight' => 2,
                    'openNewTab' => true,
                ],
                'manifesto' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'parent' => 'your-cadidate',
                    'label' => 'Manifesto',
                    'url' => '/page/manifesto',
                    'weight' => 3,
                ],
                'posts' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Posts',
                    'url' => '/posts',
                    'weight' => 4,
                ],
            ],
            'footer' => [
                'home' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Home',
                    'url' => '/',
                    'weight' => 0,
                ],
                'your-cadidate' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Your candidate',
                    'url' => '/page/your-candidate',
                    'weight' => 1,
                ],
                'posts' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Posts',
                    'url' => '/posts',
                    'weight' => 4,
                ],
                'legalities' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Legalities',
                    'url' => '/legal',
                    'weight' => 5,
                ],
                'privacy' => [
                    'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                    'label' => 'Privacy policy',
                    'url' => '/pages/privacy',
                    'weight' => 6,
                ],
            ],
        ];

        $persisted = [];
        foreach ($items as $position => $menu) {
            foreach ($menu as $id => $data) {
                $this->em->persist($persisted[$id] = MenuItem::createFixture(array_merge($data, [
                    'position' => $position,
                    'parent' => isset($data['parent']) ? $persisted[$data['parent']] : null,
                ])));
            }
        }

        $this->em->flush();
    }

    private function loadHomeBlocks()
    {
        $items = [
            [
                'type' => HomeCtaBlock::TYPE,
                'weight' => 1,
                'config' => [
                    'primary' => [
                        'label' => 'Recevoir la newsletter',
                        'target' => '/newsletter',
                        'openNewTab' => false,
                    ],
                    'secondary' => [
                        'label' => 'Lire le programme',
                        'target' => '/',
                        'openNewTab' => false,
                    ],
                ],
            ],
            [
                'type' => HomePostsBlock::TYPE,
                'weight' => 2,
                'config' => ['category' => $this->postCategories['0a767522-4a5f-4826-b0b5-612fe71ef1f1']->getId()],
            ],
            [
                'type' => HomeEventsBlock::TYPE,
                'weight' => 3,
                'config' => ['category' => $this->eventCategories['dee2afbb-f2ca-42bd-8c3e-d483a2fa3893']->getId()],
            ],
            ['type' => HomeNewsletterBlock::TYPE, 'weight' => 3],
            [
                'type' => HomeSocialsBlock::TYPE,
                'weight' => 4,
                'config' => ['facebook' => null, 'twitter' => null],
            ],
            ['type' => HomeContentBlock::TYPE, 'weight' => 5, 'config' => ['content' => '']],
        ];

        foreach ($items as $item) {
            $this->em->persist(PageBlock::createFixture(array_merge($item, [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'page' => BlockInterface::PAGE_HOME,
            ])));
        }

        $this->em->flush();
    }

    private function loadPageCategories()
    {
        $items = [
            'e2c5977a-5ddd-41b6-93b8-ccc7cea925cf' => [
                'name' => 'Health',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'weight' => 2,
            ],
            '8c21fb5c-6566-44c5-845a-34a6f536cc7e' => [
                'name' => 'Economy',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'weight' => 1,
            ],
            'fcf17dc5-4cc5-4e77-8cbe-21715824dd55' => [
                'name' => 'Category 2',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'weight' => 1,
            ],
            '59a0e6a1-79e9-4f12-97d1-df29af47d43f' => [
                'name' => 'Category 1',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'weight' => 2,
            ],
            'a5f85aee-b065-41ed-bb5a-fe294f2d0f9f' => [
                'name' => 'Acme Category',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'weight' => 1,
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->pageCategories[$id] = PageCategory::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadPages()
    {
        $items = [
            [
                'uuid' => '30f26de9-fe21-4d24-9b17-217d02156ac9',
                'title' => 'Theory of Everything',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'description' => 'Hypothetical single, all-encompassing, coherent theoretical framework of physics that fully explains all physical aspects of the universe.',
            ],
            [
                'uuid' => 'f718bd7e-18a1-4b7e-815e-75971b1c28a6',
                'title' => 'Chemistry',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'description' => 'The study of matter, its properties, how and why substances combine or separate to each other, and how substances interact with energy.',
            ],
            [
                'title' => 'Theory of relativity',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'How the Economy Will Look After the Coronavirus Pandemic',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 2',
                'categories' => [
                    $this->pageCategories['59a0e6a1-79e9-4f12-97d1-df29af47d43f'],
                    $this->pageCategories['fcf17dc5-4cc5-4e77-8cbe-21715824dd55'],
                ],
            ],
            [
                'title' => 'Coronavirus',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Coronavirus disease (COVID-19) is an infectious disease caused by a newly discovered coronavirus.',
                'image' => $this->uploads['page-image.png'],
                'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
                'categories' => [
                    $this->pageCategories['fcf17dc5-4cc5-4e77-8cbe-21715824dd55'],
                ],
            ],
            [
                'title' => 'Only for members',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Only for members',
                'categories' => [
                    $this->pageCategories['fcf17dc5-4cc5-4e77-8cbe-21715824dd55'],
                ],
                'onlyForMembers' => true,
            ],
            [
                'title' => 'Dengue and severe dengue',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 3',
            ],
            [
                'title' => 'Diabetes',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 4',
            ],
            [
                'title' => 'Ebola virus disease',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 5',
            ],
            [
                'title' => 'Alcohol',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => '5G : le lancement des enchères en France fixé à la fin septembre',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'La dette, solution et problème de la crise économique',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Crash économique sans précédent au Brésil, en pleine crise sanitaire et politique',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Les chauffeurs d\'Uber et de Lyft sont des salariés, répète le régulateur californien',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'L\'économie française a détruit un demi-million d’emplois au premier trimestre 2020',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Les élus locaux plaident pour une relance',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Stéphane Lissner: \'L\'Opéra de Paris est à genoux\'',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Emmanuel Macron, la tentation d\'une démission-réélection',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Emmanuel Macron, la tentation d\'une démission réélection',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
            ],
            [
                'title' => 'Only for members page',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'description' => 'Only for members',
                'onlyForMembers' => true,
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(Page::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadPostCategories()
    {
        $items = [
            '62686dd5-33b6-476f-bedb-bfbc3a84df0d' => [
                'name' => 'Programme',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'weight' => 2,
            ],
            '7221e3e1-df48-450d-b667-639fdc699971' => [
                'name' => 'Communiqués de presse',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'weight' => 1,
            ],
            '29c0b44c-7ed1-44c5-ada9-4622ce77d5bb' => [
                'name' => 'Category 2',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'weight' => 1,
            ],
            'd71b12f9-2b4d-45d2-ab14-b806c291aa11' => [
                'name' => 'Category 1',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'weight' => 2,
            ],
            '0a767522-4a5f-4826-b0b5-612fe71ef1f1' => [
                'name' => 'Homepage posts',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
            ],
            'f555ab0f-efcb-4a6b-9de1-90ff48a7ef24' => [
                'name' => 'Other posts',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->postCategories[$id] = PostCategory::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadPosts()
    {
        $items = [
            [
                'uuid' => '53aba31d-f8bb-483d-a5dd-2926a1d2265e',
                'title' => 'Gravitation',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'description' => 'Gravity is a natural phenomenon by which all things with mass or energy are brought toward one another',
                'publishedAt' => new \DateTime(),
            ],
            [
                'uuid' => '402e5321-33ec-4492-82d2-52690ffb762e',
                'title' => 'Theory of Relativity',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'description' => 'General relativity generalizes special relativity and refines law of universal gravitation, providing a unified description of gravity.',
                'publishedAt' => null,
            ],

            [
                'title' => 'Unpublished',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => null,
            ],
            [
                'title' => 'Scheduled',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('+6 months'),
            ],
            [
                'uuid' => '5b30949b-21dd-54a0-a05d-31f3fd1a04f2',
                'title' => 'The EU must stand with the people of Hong Kong against China’s abuse of power',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'quote' => 'Quote 1',
                'description' => 'Description 1',
                'video' => 'youtube:nxaOzonmeic',
                'image' => $this->uploads['post-image.png'],
                'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
                'publishedAt' => new \DateTime('-5 months'),
                'externalUrl' => 'https://openaction.eu',
                'categories' => [
                    $this->postCategories['29c0b44c-7ed1-44c5-ada9-4622ce77d5bb'],
                ],
            ],
            [
                'title' => 'Only for members',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-4 months'),
                'categories' => [
                    $this->postCategories['29c0b44c-7ed1-44c5-ada9-4622ce77d5bb'],
                ],
                'onlyForMembers' => true,
            ],
            [
                'title' => 'It\'s high time to improve working conditions for seasonal workers',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 2',
                'publishedAt' => new \DateTime('-10 months'),
                'categories' => [
                    $this->postCategories['d71b12f9-2b4d-45d2-ab14-b806c291aa11'],
                    $this->postCategories['29c0b44c-7ed1-44c5-ada9-4622ce77d5bb'],
                ],
            ],
            [
                'title' => 'COVID19 recovery: The EU’s response to this crisis must match its magnitude',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 3',
                'publishedAt' => new \DateTime('-7 months'),
            ],
            [
                'title' => 'EU funding to Hungary must be strictly controlled by the Commission',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 4',
                'publishedAt' => new \DateTime('-8 months'),
            ],
            [
                'title' => 'Renew Europe presents Action Plan to uphold democracy in times of COVID-19',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'description' => 'Description 5',
                'video' => 'youtube:nxaOzonmeic',
                'publishedAt' => new \DateTime('-6 months'),
            ],
            [
                'title' => 'EU Recovery plan: Either we recover together or we will fail individually',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-12 months'),
            ],
            [
                'title' => 'The EU - Western Balkans summit is a two-way commitment to European values',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-13 months'),
            ],
            [
                'title' => 'Renew Europe welcomes the European Commission\'s firm action against Poland',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-14 months'),
            ],
            [
                'title' => 'Policy paper on the use of contact tracing applications as part of the fight against COVID-19',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-15 months'),
            ],
            [
                'title' => 'EU trade relations with Mexico lifted to a new level',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-17 months'),
            ],
            [
                'title' => 'COVID-19 contact tracing apps: Only a coordinated European approach can be successful',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-16 months'),
            ],
            [
                'title' => 'COVID19 Apps: Tracing the virus should not mean tracking citizens',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-19 months'),
            ],
            [
                'title' => 'COVID-19: Much needed and efficient help for the EU’s fisheries sector',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-18 months'),
            ],
            [
                'title' => 'Renew Europe supports deprived EU citizens in the pandemic',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-20 months'),
            ],
            [
                'title' => 'COVID-19: The time has come for concrete action',
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'publishedAt' => new \DateTime('-21 months'),
            ],
            [
                'title' => 'COVID-19: The time has come for action',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'publishedAt' => new \DateTime('-21 months'),
            ],
            [
                'title' => 'On homepage',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'publishedAt' => new \DateTime('-21 months'),
                'categories' => [$this->postCategories['0a767522-4a5f-4826-b0b5-612fe71ef1f1']],
            ],
            [
                'title' => 'Not on homepage - Other category',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'publishedAt' => new \DateTime('-21 months'),
                'categories' => [$this->postCategories['f555ab0f-efcb-4a6b-9de1-90ff48a7ef24']],
            ],
            [
                'title' => 'Not on homepage - No category',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'publishedAt' => new \DateTime('-21 months'),
            ],
            [
                'title' => 'Only for members post',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'publishedAt' => new \DateTime('-4 months'),
                'onlyForMembers' => true,
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(Post::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadEventsCategories()
    {
        $items = [
            '9966b3b5-901d-4609-9cf1-ffa949987043' => [
                'name' => 'Meetups',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'weight' => 1,
            ],
            'f8d06b26-4aa3-4800-adb2-9d00880cbe17' => [
                'name' => 'Webinars',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'weight' => 2,
            ],
            '3cf44f2c-7cc7-4216-b5a0-699ffac1c1e8' => [
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'name' => 'Category 1',
                'weight' => 1,
            ],
            '00c32b15-ab4f-4155-9059-5ecd63d4ef0c' => [
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'name' => 'Category 2',
                'weight' => 2,
            ],
            'dee2afbb-f2ca-42bd-8c3e-d483a2fa3893' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'name' => 'On homepage',
                'weight' => 1,
            ],
        ];

        foreach ($items as $id => $category) {
            $this->em->persist($this->eventCategories[$id] = EventCategory::createFixture(array_merge($category, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadEvents()
    {
        $items = [
            [
                'uuid' => 'b186291d-b1ee-5458-a0f2-e31410fd26a5',
                'title' => 'Draft event',
                'description' => 'Description of the event',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
            ],
            [
                'title' => 'Published event',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'publishedAt' => new \DateTime('-7 days'),
                'beginAt' => new \DateTime('-7 days'),
                'categories' => [
                    $this->eventCategories['f8d06b26-4aa3-4800-adb2-9d00880cbe17'],
                ],
            ],
            [
                'title' => 'Scheduled event',
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'],
                'publishedAt' => new \DateTime('+7 days'),
            ],
            [
                'title' => 'Event 1',
                'slug' => 'event-1',
                'quote' => 'Event quote',
                'content' => 'Event content',
                'externalUrl' => 'https://openaction.eu',
                'publishedAt' => new \DateTime('-1 days'),
                'beginAt' => new \DateTime('+1 days'),
                'url' => 'https://citipo.com',
                'buttonText' => 'Click here',
                'latitude' => '1.2345000',
                'longitude' => '6.7890000',
                'address' => 'Event address',
                'image' => $this->uploads['event-image.png'],
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'categories' => [
                    $this->eventCategories['3cf44f2c-7cc7-4216-b5a0-699ffac1c1e8'],
                ],
                'form' => $this->forms['a2ad18d7-7cc3-4b9e-be77-900eda0262b4'],
            ],
            [
                'title' => 'Only for members',
                'slug' => 'only-for-members',
                'onlyForMembers' => true,
                'publishedAt' => new \DateTime('-19 days'),
                'beginAt' => new \DateTime('+19 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'categories' => [
                    $this->eventCategories['00c32b15-ab4f-4155-9059-5ecd63d4ef0c'],
                ],
            ],
            [
                'title' => 'Event 2',
                'slug' => 'event-2',
                'publishedAt' => new \DateTime('-19 days'),
                'beginAt' => new \DateTime('+19 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'categories' => [
                    $this->eventCategories['00c32b15-ab4f-4155-9059-5ecd63d4ef0c'],
                ],
            ],
            [
                'title' => 'Event 3',
                'slug' => 'event-3',
                'publishedAt' => new \DateTime('-18 days'),
                'beginAt' => new \DateTime('+18 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'categories' => [
                    $this->eventCategories['3cf44f2c-7cc7-4216-b5a0-699ffac1c1e8'],
                    $this->eventCategories['00c32b15-ab4f-4155-9059-5ecd63d4ef0c'],
                ],
            ],
            [
                'title' => 'Event 4',
                'slug' => 'event-4',
                'publishedAt' => new \DateTime('-17 days'),
                'beginAt' => new \DateTime('+17 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 5',
                'slug' => 'event-5',
                'publishedAt' => new \DateTime('-16 days'),
                'beginAt' => new \DateTime('+16 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 6',
                'slug' => 'event-6',
                'publishedAt' => new \DateTime('-15 days'),
                'beginAt' => new \DateTime('+15 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 7',
                'slug' => 'event-7',
                'publishedAt' => new \DateTime('-14 days'),
                'beginAt' => new \DateTime('+14 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 8',
                'slug' => 'event-8',
                'publishedAt' => new \DateTime('-13 days'),
                'beginAt' => new \DateTime('+13 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 9',
                'slug' => 'event-9',
                'publishedAt' => new \DateTime('-12 days'),
                'beginAt' => new \DateTime('+12 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 10',
                'slug' => 'event-10',
                'publishedAt' => new \DateTime('-11 days'),
                'beginAt' => new \DateTime('+11 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 11',
                'slug' => 'event-11',
                'publishedAt' => new \DateTime('-10 days'),
                'beginAt' => new \DateTime('+10 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 12',
                'slug' => 'event-12',
                'publishedAt' => new \DateTime('1 year ago'),
                'beginAt' => new \DateTime('+1 year'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event 13',
                'slug' => 'event-13',
                'publishedAt' => new \DateTime('2 years ago'),
                'beginAt' => new \DateTime('+2 years'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event unpublished',
                'slug' => 'event-unpublished',
                'beginAt' => new \DateTime('+7 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event scheduled',
                'slug' => 'event-scheduled',
                'publishedAt' => new \DateTime('+7 days'),
                'beginAt' => new \DateTime('+7 days'),
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
            ],
            [
                'title' => 'Event public',
                'slug' => 'event-public',
                'description' => 'Event description',
                'quote' => 'Event quote',
                'content' => 'Event content',
                'publishedAt' => new \DateTime('-1 days'),
                'beginAt' => new \DateTime('+1 days'),
                'url' => 'https://citipo.com',
                'buttonText' => 'Click here',
                'latitude' => '1.2345000',
                'longitude' => '6.7890000',
                'address' => 'Event address',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
            ],
            [
                'title' => 'Only for members event',
                'slug' => 'only-for-members-event',
                'onlyForMembers' => true,
                'description' => 'Only for members description',
                'quote' => 'Only for members quote',
                'content' => 'Only for members content',
                'publishedAt' => new \DateTime('-1 days'),
                'beginAt' => new \DateTime('+1 days'),
                'url' => 'https://citipo.com',
                'buttonText' => 'Only for members click here',
                'latitude' => '1.2345000',
                'longitude' => '6.7890000',
                'address' => 'Event address',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
            ],
            [
                'title' => 'Event on home',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'publishedAt' => new \DateTime('-7 days'),
                'beginAt' => new \DateTime('+7 days'),
                'categories' => [
                    $this->eventCategories['dee2afbb-f2ca-42bd-8c3e-d483a2fa3893'],
                ],
            ],
        ];

        foreach ($items as $event) {
            $this->em->persist(Event::createFixture($event));
        }

        $this->em->flush();
    }

    private function loadTrombinoscopeCategories()
    {
        $items = [
            'ee760968-6581-40ad-8b4d-af073e8943a4' => [
                'name' => 'Loire-Atlantique',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'weight' => 1,
            ],
            '4c55d963-458a-495a-85cb-67ed4a747bbe' => [
                'name' => 'Eure-et-Loir',
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'weight' => 2,
            ],
        ];

        foreach ($items as $id => $category) {
            $this->em->persist($this->trombinoscopeCategories[$id] = TrombinoscopeCategory::createFixture(array_merge($category, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadTrombinoscopePersons()
    {
        $items = [
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'fullName' => 'Nathalie Loiseau',
                'role' => 'Tête de liste Renaissance pour les élections européennes. (Île-de-France).',
                'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
                'weight' => 1,
                'socialEmail' => 'nathalie.loiseau@example.org',
                'socialFacebook' => 'https://facebook.com',
                'socialTwitter' => 'https://twitter.com',
                'socialInstagram' => 'https://instagram.com',
                'socialLinkedIn' => 'https://linkedin.com',
                'socialYoutube' => 'https:/youtube.com',
                'socialMedium' => 'https://medium.com',
                'socialTelegram' => 'nathalie.loiseau',
                'categories' => [
                    $this->trombinoscopeCategories['ee760968-6581-40ad-8b4d-af073e8943a4'],
                    $this->trombinoscopeCategories['4c55d963-458a-495a-85cb-67ed4a747bbe'],
                ],
                'publishedAt' => new \DateTime('yesterday'),
            ],
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'fullName' => 'Pascal Canfin',
                'role' => 'Ancien président de WWF (Île-de-France).',
                'weight' => 2,
                'publishedAt' => new \DateTime('+1 month'),
            ],
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'fullName' => 'Marie-Pierre Vedrenne',
                'role' => 'Juriste et directrice de la Maison de l’Europe à Rennes (Bretagne).',
                'weight' => 3,
                'categories' => [
                    $this->trombinoscopeCategories['ee760968-6581-40ad-8b4d-af073e8943a4'],
                ],
                'publishedAt' => new \DateTime('yesterday'),
            ],
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'fullName' => 'Jérémy Decerle',
                'role' => 'Exploitant agricole et président des Jeunes Agriculteurs (Bourgogne-Franche-Comté).',
                'weight' => 4,
                'categories' => [
                    $this->trombinoscopeCategories['4c55d963-458a-495a-85cb-67ed4a747bbe'],
                ],
                'publishedAt' => new \DateTime('yesterday'),
            ],
            [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'fullName' => 'Catherine Chabaud',
                'role' => 'Navigatrice et journaliste (Pays de la Loire).',
                'weight' => 5,
                'publishedAt' => new \DateTime('yesterday'),
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(TrombinoscopePerson::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadManifestoTopics()
    {
        $items = [
            '61d592f6-8435-4b7f-984a-d6b2f406c36b' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Pour une ville plus durable',
                'color' => '409B1A',
                'weight' => 1,
                'publishedAt' => new \DateTime('-1 day'),
            ],
            '2442de07-6109-45f1-a29f-f1435129e5a0' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Pour une ville plus tranquille',
                'color' => 'FF404C',
                'image' => $this->uploads['manifesto-image.png'],
                'weight' => 3,
                'publishedAt' => new \DateTime('-30 days'),
            ],
            'c79b9a7b-8581-498f-b3f1-72ef63e5e745' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Pour une ville plus sûre',
                'color' => 'A000D0',
                'weight' => 2,
                'publishedAt' => new \DateTime('-30 days'),
            ],
            'ef98bc0f-d422-4316-a4fe-f20a7e4a7c51' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Not published',
                'weight' => 4,
            ],
        ];

        foreach ($items as $uuid => $data) {
            $this->em->persist($this->manifestoTopics[$uuid] = ManifestoTopic::createFixture(array_merge($data, [
                'uuid' => $uuid,
            ])));
        }

        $this->em->flush();
    }

    private function loadManifestoProposals()
    {
        $items = [
            [
                'uuid' => '85a12e9e-921e-43a1-a12d-630de3656510',
                'topic' => $this->manifestoTopics['61d592f6-8435-4b7f-984a-d6b2f406c36b'],
                'title' => 'Donnons la priorité à vos trajets quotidiens',
                'content' => '<p>Exiger la réciprocité en matière de marchés publics</p>',
                'weight' => 1,
                'status' => ManifestoProposal::STATUS_IN_PROGRESS,
                'statusDescription' => 'Cette proposition est en cours de discution au Parlement.',
                'statusCtaText' => 'Voir les débats parlementaires',
                'statusCtaUrl' => 'https://www.youtube.com/watch?v=fQqWza-encA',
            ],
            [
                'uuid' => '5367fe7e-3c6f-4879-9e3a-f815194891f2',
                'topic' => $this->manifestoTopics['61d592f6-8435-4b7f-984a-d6b2f406c36b'],
                'title' => 'Agissons pour une ville plus propre, durablement',
                'content' => '<p>Étendre les AOP aux produits issus de l’artisanat de nos régions</p>',
                'weight' => 2,
            ],
            [
                'uuid' => '24434ee6-a0f6-4a2a-ad59-6285462af462',
                'topic' => $this->manifestoTopics['c79b9a7b-8581-498f-b3f1-72ef63e5e745'],
                'title' => 'Donnons à notre police municipale les moyens d\'être efficace.',
                'content' => '<p>Fermer toutes les centrales fonctionnant aux énergies fossiles et sortir de tous les hydrocarbures d’ici 2050</p>',
                'weight' => 1,
                'status' => ManifestoProposal::STATUS_TODO,
            ],
            [
                'uuid' => 'b0565a3c-6f68-4551-a06b-c77994119520',
                'topic' => $this->manifestoTopics['c79b9a7b-8581-498f-b3f1-72ef63e5e745'],
                'title' => 'Garantissons la sécurité de tous et la proximité pour des actions rapides. ',
                'content' => '<p>Investir au moins 1 000 milliards € pour la transition écologique d’ici 2024, pour financer les investissements indispensables (énergies et transports propres, rénovation des bâtiments...)</p>',
                'weight' => 2,
            ],
            [
                'uuid' => '73055a0d-d11b-4e3e-a162-89ac30eec1ce',
                'topic' => $this->manifestoTopics['c79b9a7b-8581-498f-b3f1-72ef63e5e745'],
                'title' => 'Encourageons les comportements civiques et sanctionnons les incivilités.',
                'content' => '<p>Taxer le carbone des produits importés en Europe et appliquer plus strictement le principe « pollueur-payeur » au sein de l’UE</p>',
                'weight' => 3,
                'status' => ManifestoProposal::STATUS_DONE,
                'statusDescription' => 'Cette proposition a été réalisée et figure désormais dans la loi.',
                'statusCtaText' => 'Lire la loi',
                'statusCtaUrl' => 'https://www.legifrance.gouv.fr',
            ],
        ];

        foreach ($items as $uuid => $data) {
            $this->em->persist(ManifestoProposal::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadForms()
    {
        $items = [
            'a2b2dbd9-f0b8-435c-ae65-00bc93ad3356' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Our Sustainable Europe',
                'description' => '15 questions for a greener Europe',
                'proposeNewsletter' => true,
                'onlyForMembers' => false,
                'redirectUrl' => 'https://example.com',
            ],
            '9cee3251-727c-4f02-ae19-22e011fada85' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Form propose newsletter without mail',
                'proposeNewsletter' => true,
                'onlyForMembers' => false,
            ],
            '6e0940f4-86a0-4739-b283-7821c2ea8843' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Form don\'t propose newsletter',
                'proposeNewsletter' => false,
                'onlyForMembers' => false,
            ],
            '1cd615eb-14af-4fd6-95ea-aec6cef62506' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Form with filtered alert',
                'proposeNewsletter' => false,
                'onlyForMembers' => false,
            ],
            '429000ee-ad24-4126-8c21-6977cbf710be' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'To be answered on phoning campaign',
                'proposeNewsletter' => false,
                'onlyForMembers' => false,
            ],
            '6df22f68-7385-4bb3-8bd9-53aa59915920' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Another one to be answered on phoning campaign',
                'proposeNewsletter' => false,
                'onlyForMembers' => false,
            ],
            'a2ad18d7-7cc3-4b9e-be77-900eda0262b4' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Event Register',
                'proposeNewsletter' => false,
            ],
            'da02014d-b48f-449c-aa13-2e1ed2e82132' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'title' => 'Only for members form',
                'proposeNewsletter' => false,
                'onlyForMembers' => true,
            ],
            'a9aea40d-5615-48fd-bdfd-55ba076388b0' => [
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'title' => 'Member file upload form',
                'proposeNewsletter' => false,
                'onlyForMembers' => true,
            ],
            '60df6024-b46a-4b1b-877b-6d34092da9da' => [
                'project' => $this->projects['062d7a3b-7cf3-48b0-b905-21f09844fb81'],
                'title' => 'Form example',
                'proposeNewsletter' => false,
                'onlyForMembers' => false,
            ],
        ];

        foreach ($items as $uuid => $data) {
            $this->em->persist($this->forms[$uuid] = Form::createFixture(array_merge($data, ['uuid' => $uuid])));
        }

        $this->em->flush();
    }

    private function loadFormsBlocks()
    {
        $items = [
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_HTML,
                'content' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/R0FSDrTjSuw" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_PARAGRAPH,
                'content' => 'To gather the views of young people on environmental challenges as well as on Europe’s ecological policy',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_PARAGRAPH,
                'content' => 'To assess the youth’s readiness to undertake meaningful changes in order to increase the sustainability of their way of life',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_PARAGRAPH,
                'content' => 'To shape policy proposals based upon data gathered',
            ],

            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_HEADER,
                'content' => 'Introductory section',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_TEXT,
                'content' => 'Age',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_RADIO,
                'content' => 'Occupation',
                'required' => true,
                'config' => [
                    'choices' => ['Student', 'Employed', 'Unemployed'],
                ],
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_RADIO,
                'content' => 'Gender',
                'required' => true,
                'config' => [
                    'choices' => ['Male', 'Female', 'Other'],
                ],
            ],

            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_HEADER,
                'content' => 'Survey',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_RATING,
                'content' => 'Would you be ready to change your transport habits for environmental reasons, even if this means a higher price or a longer commute ?',
                'required' => true,
                'config' => [
                    'choices' => [
                        'Strongly reluctant',
                        'Somewhat reluctant',
                        'Indifferent',
                        'Somewhat ready',
                        'Strongly ready',
                    ],
                ],
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_TEXTAREA,
                'content' => 'Do you have other ideas?',
            ],

            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_HEADER,
                'content' => 'Topics',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_CHECKBOX,
                'content' => 'What topics interest you the most?',
                'config' => [
                    'choices' => [
                        'Transport',
                        'Taxation',
                        'Energy',
                        'Agriculture',
                        'Education',
                    ],
                ],
            ],

            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_HEADER,
                'content' => 'When do you want to be contacted back?',
            ],

            // Automatic fields
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_EMAIL,
                'content' => 'Email',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_FORMAL_TITLE,
                'content' => 'Formal title',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_FIRST_NAME,
                'content' => 'First name',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_MIDDLE_NAME,
                'content' => 'Middle name',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_LAST_NAME,
                'content' => 'Last name',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_BIRTHDATE,
                'content' => 'Birthdate',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_GENDER,
                'content' => 'Gender',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_NATIONALITY,
                'content' => 'Nationality',
                'required' => true,
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_COMPANY,
                'content' => 'Company',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_JOB_TITLE,
                'content' => 'Job title',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_PHONE,
                'content' => 'Phone number',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_WORK_PHONE,
                'content' => 'Work phone number',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_SOCIAL_FACEBOOK,
                'content' => 'Facebook URL',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_SOCIAL_TWITTER,
                'content' => 'Twitter username',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_SOCIAL_LINKEDIN,
                'content' => 'LinkedIn URL',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_SOCIAL_TELEGRAM,
                'content' => 'Telegram username',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_SOCIAL_WHATSAPP,
                'content' => 'Whatsapp phone',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_STREET_ADDRESS,
                'content' => 'Street address',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_STREET_ADDRESS_2,
                'content' => 'Street address 2',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_CITY,
                'content' => 'City',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_ZIP_CODE,
                'content' => 'Zip code',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_COUNTRY,
                'content' => 'Country',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_TAG_RADIO,
                'content' => 'Automatic tags radio',
                'config' => [
                    'choices' => ['TagA', 'TagB', 'TagC'],
                ],
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_TAG_CHECKBOX,
                'content' => 'Automatic tags checkbox',
                'config' => [
                    'choices' => ['TagD', 'TagB', 'TagF'],
                ],
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_TAG_HIDDEN,
                'content' => 'Automatic tag (hidden)',
                'config' => [
                    'tags' => 'TagG, TagH',
                ],
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_DATE,
                'content' => 'Date',
            ],
            [
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'type' => FormBlock::TYPE_TIME,
                'content' => 'Time',
            ],

            [
                'form' => $this->forms['9cee3251-727c-4f02-ae19-22e011fada85'],
                'type' => FormBlock::TYPE_TEXT,
                'content' => 'Question',
                'required' => true,
            ],

            [
                'form' => $this->forms['6e0940f4-86a0-4739-b283-7821c2ea8843'],
                'type' => FormBlock::TYPE_TEXT,
                'content' => 'Question',
                'required' => true,
            ],

            [
                'form' => $this->forms['1cd615eb-14af-4fd6-95ea-aec6cef62506'],
                'type' => FormBlock::TYPE_TEXT,
                'content' => 'Question',
                'required' => true,
            ],

            [
                'form' => $this->forms['429000ee-ad24-4126-8c21-6977cbf710be'],
                'type' => FormBlock::TYPE_TEXT,
                'content' => 'Question',
                'required' => true,
            ],

            [
                'form' => $this->forms['6df22f68-7385-4bb3-8bd9-53aa59915920'],
                'type' => FormBlock::TYPE_TEXT,
                'content' => 'Generic question',
                'required' => true,
            ],
            [
                'form' => $this->forms['6df22f68-7385-4bb3-8bd9-53aa59915920'],
                'type' => FormBlock::TYPE_EMAIL,
                'content' => 'Email',
                'required' => true,
            ],
            [
                'form' => $this->forms['6df22f68-7385-4bb3-8bd9-53aa59915920'],
                'type' => FormBlock::TYPE_FIRST_NAME,
                'content' => 'First name',
                'required' => true,
            ],
            [
                'form' => $this->forms['6df22f68-7385-4bb3-8bd9-53aa59915920'],
                'type' => FormBlock::TYPE_LAST_NAME,
                'content' => 'Last name',
                'required' => true,
            ],

            // Member file upload form
            [
                'form' => $this->forms['a9aea40d-5615-48fd-bdfd-55ba076388b0'],
                'type' => FormBlock::TYPE_FILE,
                'content' => 'File upload',
                'required' => true,
            ],
        ];

        $weight = 1;
        foreach ($items as $data) {
            $this->em->persist(FormBlock::createFixture(array_merge($data, ['weight' => $weight])));
            ++$weight;
        }

        $this->em->flush();
    }

    private function loadFormsAnswers()
    {
        $items = [
            [
                'uuid' => 'a736779d-43b3-44e2-b2b0-5831ccb5580f',
                'form' => $this->forms['a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'],
                'contact' => $this->contacts['75d245dd-c844-4ee7-8f12-a3d611a308b6'],
                'answers' => [
                    'Titre de votre proposition' => 'Revitaliser le centre urbain',
                    'Quels sont les sujets sur lesquels porte votre proposition ?' => 'Travail',
                    'Quelles sont vos idées ?' => 'Les centres urbains sont de plus en plus désertés.',
                    'Votre prénom' => 'Titouan',
                    'Votre nom' => 'Galopin',
                    'Votre email' => 'titouan.galopin@citipo.com',
                    'Votre pays' => 'France',
                    'Votre genre' => 'Autre',
                    'Votre date de naissance' => '',
                    'A quelle heure souhaitez-vous être rencontacté(e) ?' => '10:00:00',
                ],
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(FormAnswer::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadTags()
    {
        $items = [
            'ExampleTag' => true,
            'Tag' => true,
            'StartWithTag' => true,
            'DontStartWithTag' => false,
            'ContainsTagInside' => false,
            'contains tag keyword lowercase' => false,
            'tag start with keyword lowercase' => false,
        ];

        $orga = $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'];

        $slugger = new AsciiSlugger();
        $mainTagsWeight = 1;

        foreach ($items as $tag => $isMainTag) {
            $slug = $slugger->slug($tag)->lower()->toString();

            $this->em->persist($this->tags[$slug] = Tag::createFixture([
                'orga' => $orga,
                'name' => $tag,
                'slug' => $slug,
            ]));

            if ($isMainTag) {
                $this->em->persist(new OrganizationMainTag($orga, $this->tags[$slug], $mainTagsWeight));
                ++$mainTagsWeight;
            }
        }

        $this->em->persist($orga);
        $this->em->flush();
    }

    private function loadContacts()
    {
        $items = [
            '20e51b91-bdec-495d-854d-85d6e74fc75e' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'area' => $this->areas[36778547219895752],
                'email' => 'olivie.gregoire@gmail.com',
                'contactAdditionalEmails' => ['olivie.gregoire@outlook.com'],
                'picture' => $this->uploads['contact-picture.jpg'],
                'contactPhone' => '+33757594625',
                'profileFirstName' => 'Olivie',
                'profileLastName' => 'Gregoire',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'settingsByProject' => [],
                'socialFacebook' => 'olivie.gregoire',
                'socialTwitter' => '@golivie92',
                'createdAt' => new \DateTime('+2 days'),
                'tags' => [$this->tags['exampletag'], $this->tags['startwithtag']],
                'metadataCustomFields' => [
                    'externalId' => '2485c2e31af5',
                    'donations' => [
                        ['amount' => 1000, 'date' => '2021-04-28 15:03:42'],
                        ['amount' => 2000, 'date' => '2021-05-13 09:38:11'],
                    ],
                ],
            ],
            'e90c2a1c-9504-497d-8354-c9dabc1ff7a2' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'area' => $this->areas[39389989938296926],
                'email' => 'tchalut@yahoo.fr',
                'tags' => [$this->tags['containstaginside']],
                'contactPhone' => '+33757594629',
                'profileFirstName' => 'Théodore',
                'profileLastName' => 'Chalut',
                'settingsReceiveNewsletters' => false,
                'settingsReceiveSms' => false,
                'socialTwitter' => '@theodorechalut',
                'socialLinkedIn' => 'theodore.chalut',
                'socialWhatsapp' => '+33600000000',
                'createdAt' => new \DateTime('today 2am'),
            ],

            // Null email
            '21b8f1d6-0b81-46b8-b3fe-07b3000cafb9' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'area' => $this->areas[39389989938296926],
                'email' => null,
                'tags' => [$this->tags['exampletag']],
                'profileFirstName' => 'John',
                'profileLastName' => 'Martin',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'createdAt' => new \DateTime('today 2am'),
            ],

            '8534f120-0342-46c9-aa3b-83f317335f35' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'area' => $this->areas[39389989938296926],
                'email' => 'brunella.courtemanche2@orange.fr',
                'contactPhone' => '+33757592064',
                'profileFirstName' => 'Brunella',
                'profileLastName' => 'Courtemanche',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'settingsReceiveCalls' => false,
                'socialTelegram' => 'someid',
                'socialLinkedIn' => 'brunella.courtemanche',
                'socialWhatsapp' => '+33600000001',
                'accountConfirmed' => true,
                'accountConfirmToken' => null,
                'isMember' => true,
                'metadataFlag2' => true,
                'createdAt' => new \DateTime('today 1am'),
                'tags' => [$this->tags['startwithtag']],
            ],
            '38dd80c0-b53e-4c29-806f-d2aeca8edb80' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'area' => $this->areas[65636974309722332],
                'email' => 'a.compagnon@protonmail.com',
                'contactPhone' => '+33757592579',
                'profileFirstName' => 'André',
                'profileLastName' => 'Compagnon',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'socialTelegram' => 'someid',
                'socialLinkedIn' => 'andrecompagnon',
                'tags' => [$this->tags['exampletag']],
                'accountConfirmed' => false,
                'accountConfirmToken' => '202e5f612dd9f46e4003e214a38160ad622afbc0eab5555c9c061b04186e2f32',
                'isMember' => true,
                'createdAt' => new \DateTime('-5 days'),
            ],
            '1f1b67f0-f77d-425c-9195-861b33f19695' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'area' => $this->areas[39389989938296926],
                'email' => 'apolline.mousseau@rpr.fr',
                'contactPhone' => '+33276863641',
                'profileFirstName' => 'Apolline',
                'profileLastName' => 'Mousseau',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'socialFacebook' => 'apolline.mousseau',
                'socialTwitter' => '@amousseau',
                'socialLinkedIn' => 'apollinemousseau',
                'socialTelegram' => 'secretid',
                'socialWhatsapp' => '+33601020304',
                'accountConfirmed' => true,
                'accountConfirmToken' => null,
                'accountResetToken' => 'fece487d454bdc051c6108b96e630f020ff332ad56e476fb91d05bb6a0b80121',
                'accountResetRequestedAt' => new \DateTime('30 minutes ago'),
                'isMember' => true,
                'createdAt' => new \DateTime('-2 weeks'),
            ],
            '851363e5-c97f-4c04-ba83-d98b802332c6' => [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'email' => 'julien.dubois@exampleco.com',
                'contactPhone' => '+33757594885',
                'accountConfirmed' => true,
                'accountConfirmToken' => null,
                'isMember' => true,
                'area' => $this->areas[39389989938296926],
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'createdAt' => new \DateTime('-3 weeks'),
            ],
            '75d245dd-c844-4ee7-8f12-a3d611a308b6' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'area' => $this->areas[65636974309722332],
                'email' => 'jean.marting@gmail.com',
                'contactPhone' => '+33757591557',
                'profileFirstName' => 'Jean',
                'profileLastName' => 'Martin',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'settingsReceiveCalls' => true,
                'createdAt' => new \DateTime('-4 weeks'),
            ],
            'd3e866dc-c574-4f66-ad85-53c66f98c7ad' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'area' => $this->areas[65636974309722332],
                'email' => 'jeanpaul@gmail.com',
                'contactPhone' => '+33757591559',
                'profileFirstName' => 'Jean',
                'profileLastName' => 'Paul',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'settingsReceiveCalls' => false,
                'createdAt' => new \DateTime('-2 weeks'),
                'accountConfirmed' => true,
                'accountConfirmToken' => null,
                'isMember' => true,
            ],
            '8d3323fd-e1a9-4eaa-9d4d-714abf1ff238' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'email' => 'michael.mousseau@exampleco.com',
                'contactPhone' => '+5561998812130',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'isMember' => true,
                'createdAt' => new \DateTime('-5 weeks'),
            ],

            // Essential
            '9cf2236c-732f-46c2-b65e-78a3bed256ca' => [
                'orga' => $this->orgas['eafd4a15-7812-4468-aae1-d11217667be0'],
                'email' => 'arridano.hervieux@exampleco.com',
            ],

            // Standard
            '6315217a-381a-40df-a505-6505d0a68ee9' => [
                'orga' => $this->orgas['307c3c05-1873-4e81-ae7d-a1644fa8c5a7'],
                'email' => 'vincentperreault@exampleco.com',
            ],

            // Premium
            '7cea60a5-dc7b-4677-8458-b0fe75c844bf' => [
                'orga' => $this->orgas['a54ee91a-1c37-48a1-a75d-119ac8ac798e'],
                'email' => 'etienne.jalbert@exampleco.com',
            ],

            // ambiguities
            '25c17b7c-d672-41dc-81f1-7f6d26c20503' => [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'profileFirstName' => 'John',
                'profileLastName' => 'Lennon',
                'email' => 'john@lennon.com',
                'contactPhone' => '+441234567890',
                'contactWorkPhone' => '+441234567891',
                'socialWhatsapp' => '+441234567892',
                'socialTelegram' => '+441234567893',
            ],
            'fe7d21d6-4428-4092-9df6-25b3219e0052' => [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'profileFirstName' => 'John',
                'profileLastName' => 'Lennon',
                'email' => 'another@one.com',
            ],
            'b8c0ffc5-235a-4f78-90fe-fc6fbaf79b70' => [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'profileFirstName' => 'John',
                'profileMiddleName' => 'Similar',
                'profileLastName' => 'Email',
                'email' => 'john@lennon.com',
            ],
            'd01342a4-cd62-4d86-b3e9-2d582db7797f' => [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'profileFirstName' => 'John',
                'profileMiddleName' => 'Similar',
                'profileLastName' => 'Phone',
                'email' => 'something@else.com',
                'contactPhone' => '+441234567893',
                'contactWorkPhone' => '+441234567892',
                'socialWhatsapp' => '+441234567891',
                'socialTelegram' => '+441234567890',
            ],
            '3948b42f-7a8e-46cf-bde9-90a894f9e80a' => [
                'orga' => $this->orgas['eafd4a15-7812-4468-aae1-d11217667be0'],
                'profileFirstName' => 'Corette',
                'profileMiddleName' => 'Elisabeth',
                'profileLastName' => 'Duperré',
                'email' => 'coretteduperre@teleworm.us',
                'contactPhone' => '+33422358416',
                'contactWorkPhone' => '+33368470258',
                'socialWhatsapp' => '+33131226876',
                'socialTelegram' => '+33176705142',
            ],
            'f7aa1b83-c7f3-42db-8591-a18eabb3c2b8' => [
                'orga' => $this->orgas['eafd4a15-7812-4468-aae1-d11217667be0'],
                'profileFirstName' => 'Orville',
                'profileLastName' => 'Lanoie',
                'email' => 'orvillelanoie@jourrapide.com',
                'contactPhone' => '+33313965288',
                'contactWorkPhone' => '+33413335832',
                'socialWhatsapp' => '+33167094390',
                'socialTelegram' => '+33141064129',
            ],
            '04684a46-ff0f-4110-9c14-3cb0dfbfe68b' => [
                'orga' => $this->orgas['eafd4a15-7812-4468-aae1-d11217667be0'],
                'profileFirstName' => 'Luce',
                'profileLastName' => 'Cuillerier',
                'email' => 'lucecuillerier@teleworm.us',
                'contactPhone' => '+33100232054',
                'contactWorkPhone' => '+33592145944',
                'socialWhatsapp' => '+33374701146',
                'socialTelegram' => '+33490876800',
            ],

            // member
            'da362047-7abd-40c9-8537-3d3506cb5cdb' => [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'email' => 'troycovillon@teleworm.us',
                'accountPassword' => 'password',
            ],
        ];

        foreach ($items as $id => $data) {
            $this->contacts[$id] = Contact::createFixture(array_merge($data, ['uuid' => $id]));

            if ($data['isMember'] ?? false) {
                $this->contacts[$id]->changePassword($this->hasher->hashPassword($this->contacts[$id], 'password'));
            }

            $this->em->persist($this->contacts[$id]);
            foreach ($this->contacts[$id]->getMetadataTags() as $tag) {
                $this->em->persist($tag);
            }
        }

        $this->em->flush();
    }

    private function loadContactsAmbiguous()
    {
        $items = [
            [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'oldest' => $this->contacts['25c17b7c-d672-41dc-81f1-7f6d26c20503'],
                'newest' => $this->contacts['fe7d21d6-4428-4092-9df6-25b3219e0052'],
            ],
            [
                'orga' => $this->orgas['682746ea-3e2f-4e5b-983b-6548258a2033'],
                'oldest' => $this->contacts['b8c0ffc5-235a-4f78-90fe-fc6fbaf79b70'],
                'newest' => $this->contacts['d01342a4-cd62-4d86-b3e9-2d582db7797f'],
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(Ambiguity::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadImports()
    {
        $types = [
            'ignored', 'email', 'profileFormalTitle', 'profileFirstName', 'profileMiddleName',
            'profileLastName', 'profileBirthdate', 'profileGender', 'profileCompany', 'profileJobTitle',
            'contactPhone', 'contactWorkPhone', 'socialFacebook', 'socialTwitter', 'socialLinkedIn',
            'socialTelegram', 'socialWhatsapp', 'addressStreetLine1', 'addressStreetLine2',
            'addressZipCode', 'addressCity', 'addressCountry', 'settingsReceiveNewsletters',
            'settingsReceiveSms', 'settingsReceiveCalls', 'metadataComment', 'metadataTagsList', 'metadataTag',
        ];

        $items = [
            // Not started
            'b25ca589-a613-4e62-ac0b-168b9bdf0339' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'file' => $this->uploads['import-not-started.xlsx'],
                'area' => $this->areas[64795327863947811],
                'head' => new ImportHead($types, [], $types),
            ],

            // Started
            '5deedfb6-173d-4e8b-b208-f62dbf0c4e80' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'file' => $this->uploads['import-started.xlsx'],
                'head' => new ImportHead($types, [], $types),
                'startedAt' => new \DateTime(),
            ],
            '15bcff6e-a160-4e7b-bfdc-d43a273db1a6' => [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'file' => $this->uploads['import-started-2.xlsx'],
                'head' => new ImportHead($types, [], $types),
                'startedAt' => new \DateTime(),
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->imports[$id] = Import::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadEmailingCampaigns()
    {
        $htmlContent = file_get_contents(__DIR__.'/Resources/email_campaign.html');
        $jsonContent = file_get_contents(__DIR__.'/Resources/email_campaign.json');

        $appendContent = static function (string $content, array $data) use ($htmlContent, $jsonContent): array {
            return array_merge($data, [
                'content' => str_replace('{{ content }}', $content, $htmlContent),
                'unlayerDesign' => Json::decode(str_replace('{{ content }}', $content, $jsonContent)),
            ]);
        };

        $items = [
            // Drafts
            '31fedd69-2d28-4900-8088-d28ad9606a99' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'Motivate your teams with some team building !',
                'fromEmail' => 'jbauer',
                'fromName' => 'Jacques BAUER',
                'replyToEmail' => 'reply@email.com',
                'replyToName' => 'Reply Name',
                'trackOpens' => true,
                'trackClicks' => false,
                'createdAt' => new \DateTime('2020-06-01 20:00:00'),
            ]),
            '10808026-bbae-4db5-a8ab-8abecb50102c' => $appendContent('Hello world', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => '[URGENT] Submit your applications before due date!',
                'fromEmail' => 'jbauer',
                'fromName' => 'Jacques BAUER',
                'replyToEmail' => 'reply@email.com',
                'replyToName' => 'Reply Name',
                'trackOpens' => true,
                'trackClicks' => true,
                'createdAt' => new \DateTime('2020-06-03 20:00:00'),
            ]),
            '2ed86068-e3bc-4db3-9e68-0bff1fd04fb9' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'We have the best tips for your next events',
                'fromEmail' => 'jbauer',
                'fromName' => 'Jacques BAUER',
                'replyToEmail' => 'reply@email.com',
                'replyToName' => 'Reply Name',
                'trackOpens' => true,
                'trackClicks' => true,
                'createdAt' => new \DateTime('2020-06-06 20:00:00'),
            ]),
            'ffb28a07-db46-4c56-aff5-7b7bb3dbfd48' => $appendContent('Content', [
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'subject' => 'Campaign not enough credits',
                'fromEmail' => 'julien.dubois',
                'fromName' => 'Julien DUBOIS',
                'replyToEmail' => 'reply@email.com',
                'replyToName' => 'Reply Name',
                'trackOpens' => true,
                'trackClicks' => true,
                'createdAt' => new \DateTime('2020-06-06 20:00:00'),
            ]),
            '45b9ea9c-4e62-4d7d-acf1-d7da009f657a' => $appendContent('Content', [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'subject' => 'Campaign for members',
                'fromEmail' => 'julien.dubois',
                'fromName' => 'Julien DUBOIS',
                'trackOpens' => true,
                'trackClicks' => true,
                'onlyForMembers' => true,
                'createdAt' => new \DateTime('2020-11-23 07:00:00'),
            ]),

            // Sent
            '95b3f576-c643-45ba-9d5e-c9c44f65fab8' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'Campaign without tracking',
                'fromEmail' => 'noreply',
                'fromName' => 'Citipo',
                'trackOpens' => false,
                'trackClicks' => false,
                'sentAt' => new \DateTime('2019-06-09 20:00:00'),
                'resolvedAt' => new \DateTime('2019-06-09 20:00:04'),
            ]),
            'e0340fcd-f0ec-4ee8-b0f9-7545f3a53cc5' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'Campaign with opens tracking',
                'fromEmail' => 'noreply',
                'fromName' => 'Citipo',
                'trackOpens' => true,
                'trackClicks' => false,
                'sentAt' => new \DateTime('2019-06-10 20:00:00'),
            ]),
            '9e2f34f9-de81-4303-8f39-cb5a89183316' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'Campaign with clicks tracking',
                'fromEmail' => 'noreply',
                'fromName' => 'Citipo',
                'trackOpens' => false,
                'trackClicks' => true,
                'sentAt' => new \DateTime('2019-06-10 20:00:00'),
            ]),
            '217a0dcb-bb1a-4e6d-bff1-3484c69def53' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'Campaign with clicks and opens tracking',
                'fromEmail' => 'noreply',
                'fromName' => 'Citipo',
                'trackOpens' => true,
                'trackClicks' => true,
                'sentAt' => new \DateTime('2019-06-12 20:00:00'),
            ]),
            '06afd13a-ede2-4d46-9c8c-3ad80356c41f' => $appendContent('Content', [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'subject' => 'Campaign with known stats',
                'fromEmail' => 'noreply',
                'fromName' => 'Citipo',
                'trackOpens' => true,
                'trackClicks' => true,
                'sentAt' => new \DateTime('2019-06-13 20:00:00'),
            ]),
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->emailingCampaigns[$id] = EmailingCampaign::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadEmailingCampaignsMessages()
    {
        $items = [
            [
                'campaign' => $this->emailingCampaigns['95b3f576-c643-45ba-9d5e-c9c44f65fab8'],
                'contact' => $this->contacts['851363e5-c97f-4c04-ba83-d98b802332c6'],
                'sent' => false,
            ],
            [
                'campaign' => $this->emailingCampaigns['95b3f576-c643-45ba-9d5e-c9c44f65fab8'],
                'contact' => $this->contacts['20e51b91-bdec-495d-854d-85d6e74fc75e'],
                'opened' => 3,
                'clicked' => 3,
            ],
            [
                'campaign' => $this->emailingCampaigns['95b3f576-c643-45ba-9d5e-c9c44f65fab8'],
                'contact' => $this->contacts['38dd80c0-b53e-4c29-806f-d2aeca8edb80'],
                'opened' => 4,
                'clicked' => 2,
            ],
            [
                'campaign' => $this->emailingCampaigns['e0340fcd-f0ec-4ee8-b0f9-7545f3a53cc5'],
                'contact' => $this->contacts['e90c2a1c-9504-497d-8354-c9dabc1ff7a2'],
                'opened' => 1,
            ],
            [
                'campaign' => $this->emailingCampaigns['9e2f34f9-de81-4303-8f39-cb5a89183316'],
                'contact' => $this->contacts['8534f120-0342-46c9-aa3b-83f317335f35'],
                'clicked' => 1,
            ],
            [
                'campaign' => $this->emailingCampaigns['45b9ea9c-4e62-4d7d-acf1-d7da009f657a'],
                'contact' => $this->contacts['75d245dd-c844-4ee7-8f12-a3d611a308b6'],
                'clicked' => 1,
            ],
            [
                'campaign' => $this->emailingCampaigns['217a0dcb-bb1a-4e6d-bff1-3484c69def53'],
                'contact' => $this->contacts['38dd80c0-b53e-4c29-806f-d2aeca8edb80'],
                'bounced' => true,
            ],
        ];

        for ($i = 0; $i < 100; ++$i) {
            $items[] = [
                'campaign' => $this->emailingCampaigns['06afd13a-ede2-4d46-9c8c-3ad80356c41f'],
                'contact' => $this->contacts['1f1b67f0-f77d-425c-9195-861b33f19695'],
                'sent' => $i < 90,
                'bounced' => $i < 5,
                'opened' => $i < 40 ? 1 : 0,
                'clicked' => $i < 10 ? 1 : 0,
            ];
        }

        foreach ($items as $data) {
            $this->em->persist(EmailingCampaignMessage::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadTextingCampaigns()
    {
        $items = [
            // Acme
            'e4a3799d-b217-4389-b89e-beef08bdbbd3' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'content' => 'First campaign',
                'createdAt' => new \DateTime('2020-06-01 20:00:00'),
            ],
            '5030c62f-583a-40a5-b301-32db2f078e6d' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'content' => 'Something else',
                'createdAt' => new \DateTime('2019-06-09 20:00:00'),
            ],
            '9b5a951e-5b2b-43f6-923e-ff08fa73ce03' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'content' => 'Another one',
                'createdAt' => new \DateTime('2019-06-09 20:00:00'),
            ],
            '60c2bdb2-d071-4b07-ac62-0ea2d90bc947' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'content' => 'Sent campaign',
                'sentAt' => new \DateTime('2019-06-09 20:00:00'),
                'resolvedAt' => new \DateTime('2019-06-09 20:00:04'),
            ],
            'c5a63aa8-e3b0-4ee8-a61b-087e655f77e7' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'content' => 'Who wrote this?',
                'sentAt' => new \DateTime('2019-06-09 20:00:00'),
                'resolvedAt' => new \DateTime('2019-06-09 20:00:04'),
            ],

            // Citipo
            'c4d39567-f3ef-4f46-ac2f-d7573a5456d9' => [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'],
                'content' => 'Go vote for Auralp on 20th and 27th of June!',
                'createdAt' => new \DateTime('2019-06-09 20:00:00'),
            ],

            // Example Co.
            '197efcd3-00ea-470e-8b47-99f84ff7c128' => [
                'project' => $this->projects['643e47ea-fd9d-4963-958f-05970de2f88b'],
                'content' => 'Campaign not enough credits',
                'createdAt' => new \DateTime('2020-06-06 20:00:00'),
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->textingCampaigns[$id] = TextingCampaign::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadTextingCampaignsMessages()
    {
        $items = [
            [
                'campaign' => $this->textingCampaigns['c4d39567-f3ef-4f46-ac2f-d7573a5456d9'],
                'contact' => $this->contacts['75d245dd-c844-4ee7-8f12-a3d611a308b6'],
                'sent' => false,
            ],
            [
                'campaign' => $this->textingCampaigns['60c2bdb2-d071-4b07-ac62-0ea2d90bc947'],
                'contact' => $this->contacts['8d3323fd-e1a9-4eaa-9d4d-714abf1ff238'],
                'sent' => true,
            ],
        ];

        for ($i = 0; $i < 100; ++$i) {
            $items[] = [
                'campaign' => $this->textingCampaigns['c5a63aa8-e3b0-4ee8-a61b-087e655f77e7'],
                'contact' => $this->contacts['1f1b67f0-f77d-425c-9195-861b33f19695'],
                'sent' => $i < 42,
            ];
        }

        foreach ($items as $data) {
            $this->em->persist(TextingCampaignMessage::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadPhoningCampaigns()
    {
        $items = [
            // Draft
            'e5a632df-4960-4d56-bc94-944e0879268e' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'name' => 'Draft campaign',
                'form' => $this->forms['429000ee-ad24-4126-8c21-6977cbf710be'],
            ],

            // Active
            '186314e6-7097-4ad6-9ba1-82030892fcf0' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'name' => 'Active campaign',
                'form' => $this->forms['6df22f68-7385-4bb3-8bd9-53aa59915920'],
                'startAt' => new \DateTime('-1 day'),
                'resolvedAt' => new \DateTime('-1 day'),
                'endAfter' => 720, // 1 month
            ],

            // Finished
            '8b47f627-22af-4828-b66e-044511ee3902' => [
                'project' => $this->projects['2c720420-65fd-4360-9d77-731758008497'],
                'name' => 'Finished campaign',
                'startAt' => new \DateTime('2019-12-25 20:00:00'),
                'endAfter' => 48,
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->phoningCampaigns[$id] = PhoningCampaign::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadPhoningCampaignsTargets()
    {
        $items = [
            '129d1dc6-5c16-46a8-bd60-718b47f37551' => [
                'campaign' => $this->phoningCampaigns['186314e6-7097-4ad6-9ba1-82030892fcf0'],
                'contact' => $this->contacts['75d245dd-c844-4ee7-8f12-a3d611a308b6'],
            ],
            'd35fc104-6b15-4441-9a96-5f292143eec2' => [
                'campaign' => $this->phoningCampaigns['186314e6-7097-4ad6-9ba1-82030892fcf0'],
                'contact' => $this->contacts['20e51b91-bdec-495d-854d-85d6e74fc75e'],
            ],
            '458c6157-d475-4fa9-9aec-59ae80cb5eff' => [
                'campaign' => $this->phoningCampaigns['186314e6-7097-4ad6-9ba1-82030892fcf0'],
                'contact' => $this->contacts['38dd80c0-b53e-4c29-806f-d2aeca8edb80'],
            ],
            '21ca55b6-4189-478b-9b47-fe52f4fb2d62' => [
                'campaign' => $this->phoningCampaigns['8b47f627-22af-4828-b66e-044511ee3902'],
                'contact' => $this->contacts['38dd80c0-b53e-4c29-806f-d2aeca8edb80'],
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->phoningCampaignsTargets[$id] = PhoningCampaignTarget::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadPhoningCampaignsCalls()
    {
        $items = [
            'bb133d20-f339-4488-96c2-498c80e01a1d' => [
                'target' => $this->phoningCampaignsTargets['129d1dc6-5c16-46a8-bd60-718b47f37551'],
                'author' => $this->contacts['d3e866dc-c574-4f66-ad85-53c66f98c7ad'],
            ],
        ];

        foreach ($items as $id => $data) {
            $this->em->persist(PhoningCampaignCall::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadEmailAutomations()
    {
        $htmlContent = file_get_contents(__DIR__.'/Resources/email_campaign.html');
        $jsonContent = file_get_contents(__DIR__.'/Resources/email_campaign.json');

        $appendContent = static function (string $content, array $data) use ($htmlContent, $jsonContent): array {
            return array_merge($data, [
                'content' => str_replace('{{ content }}', $content, $htmlContent),
                'unlayerDesign' => Json::decode(str_replace('{{ content }}', $content, $jsonContent)),
            ]);
        };

        $items = [
            // Disabled
            '5c232818-ebb3-4a07-bb3b-2732082fb26c' => $appendContent('Content', [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'name' => 'Disabled automation',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'subject' => 'Disabled automation subject',
                'weight' => 1,
                'enabled' => false,
            ]),

            // Enabled alert to admins
            '828e0d22-0fab-4a59-a9d6-9b5dc575680f' => $appendContent('Contact [fullName]', [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'name' => 'Admin alert automation',
                'toEmail' => 'contact@citipo.com',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'subject' => 'New contact alert',
                'weight' => 2,
            ]),

            // Enabled welcome message to members
            '47c79d3b-bdbb-4553-b309-14f44d7a6124' => $appendContent('Welcome [fullName]', [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'name' => 'Member welcome message',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'typeFilter' => EmailAutomation::TYPE_MEMBER,
                'subject' => 'Welcome !',
                'weight' => 3,
            ]),

            // Tag filter
            'c61a2198-905d-4e1f-af2f-0e432b430885' => $appendContent('Tag alert [fullName]', [
                'orga' => $this->orgas['219025aa-7fe2-4385-ad8f-31f386720d10'],
                'name' => 'Filtered tag alert',
                'toEmail' => 'contact@citipo.com',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'subject' => 'Filtered tag alert',
                'weight' => 4,
                'trigger' => EmailAutomation::TRIGGER_CONTACT_TAGGED,
                'tagFilter' => $this->tags['exampletag'],
            ]),

            // Acme automations for forms
            'cbd064c2-d724-4817-99d4-f9b6ff53ef75' => $appendContent('Contact [fullName]', [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'name' => 'Admin alert automation contact',
                'toEmail' => 'contact@citipo.com',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'subject' => 'New contact alert',
                'weight' => 2,
            ]),

            'f2185892-9b3a-4eab-8a81-3520cded8571' => $appendContent('Title: -form-title-', [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'name' => 'Admin alert automation form',
                'toEmail' => 'contact@citipo.com',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'subject' => 'New form answer alert',
                'weight' => 3,
                'trigger' => EmailAutomation::TRIGGER_NEW_FORM_ANSWER,
            ]),

            'fc796193-d9ab-41b3-b19c-9f296b1f36a8' => $appendContent('Title: -form-title-, answer: -form-answer-1-', [
                'orga' => $this->orgas['cbeb774c-284c-43e3-923a-5a2388340f91'],
                'name' => 'Filtered form alert',
                'toEmail' => 'contact@citipo.com',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'subject' => 'Filtered form alert',
                'weight' => 4,
                'trigger' => EmailAutomation::TRIGGER_NEW_FORM_ANSWER,
                'formFilter' => $this->forms['1cd615eb-14af-4fd6-95ea-aec6cef62506'],
            ]),
        ];

        foreach ($items as $id => $data) {
            $this->em->persist($this->emailAutomations[$id] = EmailAutomation::createFixture(array_merge($data, ['uuid' => $id])));
        }

        $this->em->flush();
    }

    private function loadEmailAutomationsMessages()
    {
        $items = [
            [
                'automation' => $this->emailAutomations['828e0d22-0fab-4a59-a9d6-9b5dc575680f'],
                'email' => 'olivie.gregoire@gmail.com',
            ],
            [
                'automation' => $this->emailAutomations['5c232818-ebb3-4a07-bb3b-2732082fb26c'],
                'email' => 'olivie.gregoire@gmail.com',
            ],
        ];

        foreach ($items as $data) {
            $this->em->persist(EmailAutomationMessage::createFixture($data));
        }

        $this->em->flush();
    }

    private function loadAnalyticsPageViews()
    {
        $query = $this->em->getConnection()->prepare('
            INSERT INTO analytics_website_page_views
                (id, project_id, hash, path, platform, browser, country, referrer, referrer_path, utm_source, utm_medium, utm_campaign, utm_content, date)
            VALUES
                (nextval(\'analytics_website_page_views_id_seq\'), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        foreach (PageViews::createDataEnding('yesterday') as $row) {
            $query->execute([
                $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87']->getId(),
                $row['hash'],
                $row['path'],
                $row['platform'],
                $row['browser'],
                $row['country'],
                $row['referrer'],
                $row['referrer_path'],
                $row['utm_source'],
                $row['utm_medium'],
                $row['utm_campaign'],
                $row['utm_content'],
                $row['date'],
            ]);
        }
    }

    private function loadAnalyticsSessions()
    {
        $query = $this->em->getConnection()->prepare('
            INSERT INTO analytics_website_sessions
                (id, organization_id, project_id, hash, paths_flow, paths_count, platform, browser, country, original_referrer, start_date, end_date, utm_source, utm_medium, utm_campaign)
            VALUES
                (nextval(\'analytics_website_sessions_id_seq\'), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        foreach (Sessions::createDataEnding('yesterday') as $row) {
            $query->execute([
                $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7']->getOrganization()->getId(),
                $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7']->getId(),
                $row['hash'],
                $row['paths_flow'],
                $row['paths_count'],
                $row['platform'],
                $row['browser'],
                $row['country'],
                $row['original_referrer'],
                $row['start_date'],
                $row['end_date'],
                $row['utm_source'],
                $row['utm_medium'],
                $row['utm_campaign'],
            ]);
        }
    }

    private function loadAnalyticsEvents()
    {
        $query = $this->em->getConnection()->prepare('
            INSERT INTO analytics_website_events (id, project_id, hash, name, date)
            VALUES (nextval(\'analytics_website_events_id_seq\'), ?, ?, ?, ?)
        ');

        foreach (Events::createDataEnding('yesterday') as $row) {
            $query->execute([
                $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7']->getId(),
                $row['hash'],
                $row['name'],
                $row['date'],
            ]);
        }
    }

    private function loadAnalyticsContactsCreations()
    {
        $statQuery = $this->em->getConnection()->prepare('
            INSERT INTO analytics_community_contact_creations
                (contact_id, organization_id, project_id, is_member, has_phone, receives_newsletter,
                 receives_sms, country, tags, gender, date, id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, nextval(\'analytics_community_contact_creations_id_seq\'))
        ');

        $data = [
            // olivie.gregoire@gmail.com
            [
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'], // Global
                'contact' => $this->contacts['20e51b91-bdec-495d-854d-85d6e74fc75e'],
            ],

            // a.compagnon@protonmail.com
            [
                'project' => $this->projects['e816bcc6-0568-46d1-b0c5-917ce4810a87'], // Global
                'contact' => $this->contacts['38dd80c0-b53e-4c29-806f-d2aeca8edb80'],
            ],
            [
                'project' => $this->projects['151f1340-9ad6-47c7-a8a5-838ff955eae7'], // Local
                'contact' => $this->contacts['38dd80c0-b53e-4c29-806f-d2aeca8edb80'],
            ],
        ];

        foreach ($data as $item) {
            /** @var Contact $contact */
            $contact = $item['contact'];

            $statQuery->execute([
                $contact->getId(),
                $contact->getOrganization()->getId(),
                $item['project']->getId(),
                $contact->isMember() ? 'true' : 'false',
                $contact->getParsedContactPhone() ? 'true' : 'false',
                $contact->hasSettingsReceiveNewsletters() ? 'true' : 'false',
                $contact->hasSettingsReceiveSms() ? 'true' : 'false',
                $contact->getAddressCountry()?->getCode(),
                Json::encode($contact->getMetadataTagsNames()),
                $contact->getProfileGender(),
                $contact->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
