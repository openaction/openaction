<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Controller\AbstractController;
use App\Entity\Community\PrintingOrder;
use App\Form\Community\Printing\Model\PrintingOrderRecipientData;
use App\Form\Community\Printing\PrintingOrderRecipientType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/printing/{uuid}/recipient')]
class RecipientController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: 'console_community_printing_recipient', methods: ['GET', 'POST'])]
    public function recipient(Request $request, PrintingOrder $order)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($order);

        // Already ordered campaigns can't be edited anymore
        if ($order->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        $data = PrintingOrderRecipientData::fromOrder($order);

        $form = $this->createForm(PrintingOrderRecipientType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->applyRecipientData($data);

            $this->em->persist($order);
            $this->em->flush();

            $this->addFlash('success', 'printing.updated_success');

            return $this->redirectToRoute('console_community_printing_recipient', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $order->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/finalize/recipient.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}
