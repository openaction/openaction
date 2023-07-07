<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Bridge\Uploadcare\UploadcareInterface;
use App\Community\Printing\Consumer\DownloadSourceMessage;
use App\Community\Printing\PrintingWorkflow;
use App\Controller\AbstractController;
use App\Entity\Community\PrintingCampaign;
use App\Platform\Permissions;
use App\Platform\PrintFiles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/printing')]
class BatController extends AbstractController
{
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;
    private PrintingWorkflow $workflow;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus, PrintingWorkflow $workflow)
    {
        $this->em = $em;
        $this->bus = $bus;
        $this->workflow = $workflow;
    }

    #[Route('/{uuid}/bat', name: 'console_community_printing_bat')]
    public function bat(PrintingCampaign $campaign, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $project);
        $this->denyIfSubscriptionExpired();

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->workflow->validateBat($campaign);

            $this->addFlash('success', 'printing.updated_success');

            return $this->redirectToRoute('console_community_printing_view', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $campaign->getPrintingOrder()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/printing/bat/bat.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{uuid}/bat/reconfigure', name: 'console_community_printing_bat_reconfigure', methods: ['GET', 'POST'])]
    public function reconfigure(UploadcareInterface $uploadcare, PrintingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign->getPrintingOrder());

        return $this->render('console/project/community/printing/bat/reconfigure.html.twig', [
            'campaign' => $campaign,
            'expectedSize' => PrintFiles::SIZE_BY_PRODUCT[$campaign->getProduct()],
            'expectedPages' => PrintFiles::PAGES_BY_PRODUCT[$campaign->getProduct()],
            'uploadKey' => $uploadcare->generateUploadKey(),
        ]);
    }

    #[Route('/{uuid}/bat/upload', name: 'console_community_printing_bat_upload', methods: ['POST'])]
    public function upload(PrintingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign->getPrintingOrder());
        $this->denyUnlessValidCsrf($request);

        // Clear old files
        $batToRemove = $campaign->getBat();
        $previewToRemove = $campaign->getPreview();
        $sourceToRemove = $campaign->getSource();

        $campaign->updateSourceFile(null, null);
        $campaign->setSourceError(null);
        $campaign->receiveBat(null, []);
        $this->em->persist($campaign);

        if ($batToRemove) {
            $this->em->remove($batToRemove);
        }

        if ($previewToRemove) {
            $this->em->remove($previewToRemove);
        }

        if ($sourceToRemove) {
            $this->em->remove($sourceToRemove);
        }

        $this->em->flush();

        // Start downloading new file
        $this->bus->dispatch(new DownloadSourceMessage($campaign->getId(), $request->query->get('fileUuid'), true));

        return new JsonResponse(['status' => 'handled']);
    }

    #[Route('/{uuid}/bat/status', name: 'console_community_printing_bat_status', methods: ['GET'])]
    public function status(PrintingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_ORDER, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign->getPrintingOrder());

        return new JsonResponse([
            'success' => null !== $campaign->getSource(),
            'errors' => $campaign->getSourceError()?->getMessages() ?: [],
        ]);
    }
}
