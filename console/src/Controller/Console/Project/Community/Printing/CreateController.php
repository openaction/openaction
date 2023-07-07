<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Controller\AbstractController;
use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Form\Community\Printing\CreatePrintingCampaignType;
use App\Form\Community\Printing\Model\CreatePrintingCampaignData;
use App\Platform\Permissions;
use App\Platform\Products;
use App\Repository\Community\PrintingOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/printing')]
class CreateController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(PrintingOrderRepository $r, EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/create/{uuid}', defaults: ['uuid' => null], name: 'console_community_printing_create')]
    public function create(Request $request, PrintingOrder $order = null)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $data = new CreatePrintingCampaignData();

        $form = $this->createForm(CreatePrintingCampaignType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$order) {
                $order = new PrintingOrder($this->getProject());
                $this->em->persist($order);
            }

            $this->em->persist(new PrintingCampaign($order, $data->product));
            $this->em->flush();

            return $this->redirectToRoute('console_community_printing', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/create.html.twig', [
            'form' => $form->createView(),
            'order_has_campaigns' => $order && $order->hasCampaigns(),
            'order_is_official' => $order && $order->isOfficialOrder(),
        ]);
    }

    #[Route('/create-kit/{kit}', requirements: ['kit' => 'official'], name: 'console_community_printing_create_kit')]
    public function createKit(Request $request, string $kit)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if ('official' === $kit) {
            $order = new PrintingOrder($this->getProject());
            $this->em->persist($order);

            $this->em->persist(new PrintingCampaign($order, Products::PRINT_OFFICIAL_POSTER));
            $this->em->persist(new PrintingCampaign($order, Products::PRINT_OFFICIAL_BANNER));
            $this->em->persist(new PrintingCampaign($order, Products::PRINT_OFFICIAL_PLEDGE));
            $this->em->persist(new PrintingCampaign($order, Products::PRINT_OFFICIAL_BALLOT));
            $this->em->flush();
        }

        return $this->redirectToRoute('console_community_printing', [
            'projectUuid' => $this->getProject()->getUuid(),
        ]);
    }
}
