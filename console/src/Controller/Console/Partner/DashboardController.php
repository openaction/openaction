<?php

namespace App\Controller\Console\Partner;

use App\Controller\AbstractController;
use App\Dashboard\DashboardBuilder;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/partner/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'console_partner_dashboard', methods: ['GET'])]
    public function manage(DashboardBuilder $dashboardBuilder)
    {
        return $this->render('console/partner/dashboard.html.twig', [
            'dashboard' => $dashboardBuilder->createPartnerDashboard($this->getUser()),
        ]);
    }
}
