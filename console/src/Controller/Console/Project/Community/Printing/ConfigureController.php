<?php

namespace App\Controller\Console\Project\Community\Printing;

use App\Bridge\Uploadcare\UploadcareInterface;
use App\Community\Printing\Consumer\DownloadSourceMessage;
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
class ConfigureController extends AbstractController
{
    #[Route('/{uuid}/configure', name: 'console_community_printing_configure', methods: ['GET', 'POST'])]
    public function configure(UploadcareInterface $uploadcare, PrintingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign->getPrintingOrder());

        // Already ordered campaigns can't be edited anymore
        if ($campaign->getPrintingOrder()->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('console/project/community/printing/configure/form.html.twig', [
            'campaign' => $campaign,
            'expectedSize' => PrintFiles::SIZE_BY_PRODUCT[$campaign->getProduct()],
            'expectedPages' => PrintFiles::PAGES_BY_PRODUCT[$campaign->getProduct()],
            'uploadKey' => $uploadcare->generateUploadKey(),
        ]);
    }

    #[Route('/{uuid}/upload', name: 'console_community_printing_upload', methods: ['POST'])]
    public function upload(EntityManagerInterface $em, MessageBusInterface $bus, PrintingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign->getPrintingOrder());
        $this->denyUnlessValidCsrf($request);

        // Already ordered campaigns can't be edited anymore
        if ($campaign->getPrintingOrder()->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        // Clear old files
        $previewToRemove = $campaign->getPreview();
        $sourceToRemove = $campaign->getSource();

        $campaign->updateSourceFile(null, null);
        $campaign->setSourceError(null);
        $em->persist($campaign);

        if ($previewToRemove) {
            $em->remove($previewToRemove);
        }

        if ($sourceToRemove) {
            $em->remove($sourceToRemove);
        }

        $em->flush();

        // Start downloading new file
        $bus->dispatch(new DownloadSourceMessage($campaign->getId(), $request->query->get('fileUuid')));

        return new JsonResponse(['status' => 'handled']);
    }

    #[Route('/{uuid}/status', name: 'console_community_printing_status', methods: ['GET'])]
    public function status(PrintingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PRINTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign->getPrintingOrder());

        // Already ordered campaigns can't be edited anymore
        if ($campaign->getPrintingOrder()->getOrder()) {
            throw $this->createAccessDeniedException();
        }

        return new JsonResponse([
            'success' => null !== $campaign->getSource(),
            'errors' => $campaign->getSourceError()?->getMessages() ?: [],
        ]);
    }
}
