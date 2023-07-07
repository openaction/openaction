<?php

namespace App\Controller\Admin;

use App\Analytics\Analytics;
use App\Billing\Stats\SubscriptionsStats;
use App\Entity\Announcement;
use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Entity\Community\PrintingOrder;
use App\Entity\Domain;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Registration;
use App\Entity\User;
use App\Platform\Features;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Util\Chart;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private bool $isOnPremise;
    private ContainerInterface $locator;

    public function __construct(bool $isOnPremise, ContainerInterface $locator)
    {
        $this->isOnPremise = $isOnPremise;
        $this->locator = $locator;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            Analytics::class,
            SubscriptionsStats::class,
            OrganizationRepository::class,
            ProjectRepository::class,
            UserRepository::class,
            EntityManagerInterface::class,
        ]);
    }

    #[Route('/admin', name: 'easyadmin')]
    public function index(): Response
    {
        $startDate = new \DateTime('60 days ago 00:00:00');

        return $this->render('admin/dashboard.html.twig', [
            'organizations_count' => $this->locator->get(ProjectRepository::class)->count([]),
            'users_count' => $this->locator->get(UserRepository::class)->count([]),
            'all_projects_count' => $this->locator->get(ProjectRepository::class)->count([]),
            'web_projects_count' => $this->locator->get(ProjectRepository::class)->countByTool(Features::TOOL_WEBSITE_PAGES),
            'print_projects_count' => $this->locator->get(ProjectRepository::class)->countByTool(Features::TOOL_COMMUNITY_PRINTING),
            'live_visitors' => $this->locator->get(Analytics::class)->countAdminLiveVisitors(),
            'traffic_dashboard' => $this->locator->get(Analytics::class)->createAdminTrafficDashboard($startDate, Chart::PRECISION_DAY),
            'community_dashboard' => $this->locator->get(Analytics::class)->createAdminCommunityDashboard($startDate, Chart::PRECISION_DAY),
        ]);
    }

    #[Route('/admin/subscriptions', name: 'admin_subscriptions_dashboard')]
    public function subscriptions(): Response
    {
        return $this->render('admin/subscriptions_dashboard.html.twig', [
            'active_subscriptions' => $this->locator->get(OrganizationRepository::class)->countActiveSubscriptions(),
            'trialing_subscriptions' => $this->locator->get(OrganizationRepository::class)->countTrialingSubscriptions(),
            'expired_subscriptions' => $this->locator->get(OrganizationRepository::class)->countExpiredSubscriptions(),
            'almost_expired_subscriptions' => $this->locator->get(OrganizationRepository::class)->findAlmostExpiredPayingSubscriptions(),
            'mrr' => $this->locator->get(SubscriptionsStats::class)->computeMonthlyRecurringRevenue(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Citipo')
            ->setFaviconPath('android-chrome-192x192.png')
        ;
    }

    public function configureMenuItems(): iterable
    {
        /** @var EntityManagerInterface $em */
        $em = $this->locator->get(EntityManagerInterface::class);

        yield MenuItem::linkToDashboard('General dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Back to Console', 'fa fa-share-square', 'homepage_redirect');
        yield MenuItem::linkToRoute('Log out', 'fa fa-sign-out', 'security_logout');

        /*
         * Platform
         */
        yield MenuItem::section('Platform');

        yield MenuItem::linkToCrud('Announcements', 'fa fa-bullhorn', Announcement::class);

        /*
         * Billing
         */
        yield MenuItem::section('Billing');

        yield MenuItem::linkToRoute('Billing dashboard', 'fa fa-home', 'admin_subscriptions_dashboard');
        yield MenuItem::linkToCrud('Orders', 'fa fa-file-invoice-dollar', Order::class)
            ->setBadge($em->getRepository(Order::class)->count([]));
        yield MenuItem::linkToCrud('Quotes', 'fa fa-file-invoice-dollar', Quote::class)
            ->setBadge($em->getRepository(Quote::class)->count([]));

        /*
         * Organizations
         */
        yield MenuItem::section('Organizations');

        if ($this->isOnPremise) {
            yield MenuItem::linkToRoute('New organization', 'fa fa-plus', 'admin_start_on_premise');
        } else {
            yield MenuItem::linkToRoute('Empty organization', 'fa fa-plus', 'admin_start_trial');
            yield MenuItem::linkToRoute('AvecVous organization', 'fa fa-plus', 'admin_start_on_premise');
        }

        yield MenuItem::linkToCrud('Organizations', 'fa fa-building', Organization::class)
            ->setBadge($em->getRepository(Organization::class)->count([]));
        yield MenuItem::linkToCrud('Domains', 'fa fa-globe', Domain::class)
            ->setBadge($em->getRepository(Domain::class)->count([]));
        yield MenuItem::linkToCrud('Projects', 'fa fa-cube', Project::class)
            ->setBadge($em->getRepository(Project::class)->count([]));
        yield MenuItem::linkToRoute('Export projects', 'fa fa-cloud-download', 'admin_projects_export')
            ->setLinkTarget('_blank');

        /*
         * Users
         */
        yield MenuItem::section('Users');

        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
            ->setBadge($em->getRepository(User::class)->count([]));

        yield MenuItem::linkToCrud('Registrations', 'fa fa-envelope', Registration::class);

        /*
         * Print
         */
        yield MenuItem::section('Print');

        yield MenuItem::linkToRoute('Print dashboard', 'fa fa-home', 'admin_print_dashboard');

        yield MenuItem::linkToCrud('Ordered', 'fa fa-print', PrintingOrder::class)
            ->setBadge($em->getRepository(PrintingOrder::class)->countByOrderedStatus(true))
            ->setQueryParameter('status', 'ordered');

        yield MenuItem::linkToCrud('Drafts', 'fa fa-print', PrintingOrder::class)
            ->setBadge($em->getRepository(PrintingOrder::class)->countByOrderedStatus(false))
            ->setQueryParameter('status', 'draft');

        yield MenuItem::linkToRoute('Export orders to print', 'fa fa-cloud-download', 'admin_print_export_to_print')
            ->setLinkTarget('_blank');
        yield MenuItem::linkToRoute('Export ordered orders', 'fa fa-cloud-download', 'admin_print_export_ordered')
            ->setLinkTarget('_blank');
        yield MenuItem::linkToRoute('Export all orders', 'fa fa-cloud-download', 'admin_print_export_all')
            ->setLinkTarget('_blank');
    }
}
