<?php

namespace App\Controller\Admin;

use App\Admin\DashboardStatsResolver;
use App\Analytics\Analytics;
use App\Billing\Stats\SubscriptionsStats;
use App\Entity\Announcement;
use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Entity\Domain;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Registration;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly bool $isOnPremise,
        private readonly DashboardStatsResolver $dashboardStatsResolver,
        private readonly EntityManagerInterface $manager,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
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
        return $this->render('admin/dashboard.html.twig', $this->dashboardStatsResolver->getAdminIndexStats());
    }

    #[Route('/admin/subscriptions', name: 'admin_subscriptions_dashboard')]
    public function subscriptions(): Response
    {
        return $this->render('admin/subscriptions_dashboard.html.twig', $this->dashboardStatsResolver->getAdminBillingStats());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('OpenAction')
            ->setFaviconPath('android-chrome-192x192.png')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('General dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Back to Console', 'fa fa-share-square', 'homepage_redirect');
        yield MenuItem::linkToRoute('Log out', 'fa fa-sign-out', 'security_logout');

        /*
         * Platform
         */
        yield MenuItem::section('Platform');

        yield MenuItem::linkToCrud('Announcements', 'fa fa-bullhorn', Announcement::class);

        if (!$this->isOnPremise) {
            /*
             * Billing
             */
            yield MenuItem::section('Billing');

            yield MenuItem::linkToRoute('Billing dashboard', 'fa fa-home', 'admin_subscriptions_dashboard');
            yield MenuItem::linkToCrud('Orders', 'fa fa-file-invoice-dollar', Order::class)
                ->setBadge($this->manager->getRepository(Order::class)->count([]));
            yield MenuItem::linkToCrud('Quotes', 'fa fa-file-invoice-dollar', Quote::class)
                ->setBadge($this->manager->getRepository(Quote::class)->count([]));
        }

        /*
         * Organizations
         */
        yield MenuItem::section('Organizations');

        if ($this->isOnPremise) {
            yield MenuItem::linkToRoute('New organization', 'fa fa-plus', 'admin_start_on_premise');
        } else {
            yield MenuItem::linkToRoute('Empty organization', 'fa fa-plus', 'admin_start_trial');
            yield MenuItem::linkToRoute('Landing page organization', 'fa fa-plus', 'admin_start_on_premise');
        }

        yield MenuItem::linkToCrud('Organizations', 'fa fa-building', Organization::class)
            ->setBadge($this->manager->getRepository(Organization::class)->count([]));
        yield MenuItem::linkToCrud('Domains', 'fa fa-globe', Domain::class)
            ->setBadge($this->manager->getRepository(Domain::class)->count([]));
        yield MenuItem::linkToCrud('Projects', 'fa fa-cube', Project::class)
            ->setBadge($this->manager->getRepository(Project::class)->count([]));
        yield MenuItem::linkToRoute('Export projects', 'fa fa-cloud-download', 'admin_projects_export')
            ->setLinkTarget('_blank');
        yield MenuItem::linkToUrl('Turnstile', 'fa fa-robot', $this->adminUrlGenerator
            ->setController(ProjectController::class)
            ->setAction('manageTurnstile')
            ->setEntityId(null)
        );

        /*
         * Users
         */
        yield MenuItem::section('Users');

        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
            ->setBadge($this->manager->getRepository(User::class)->count([]));

        yield MenuItem::linkToCrud('Registrations', 'fa fa-envelope', Registration::class);
    }
}
