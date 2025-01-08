<?php

namespace App\Controller\Console\Project\Community\Emailing;

use App\Community\ImportExport\EmailingCampaignExporter;
use App\Controller\AbstractController;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\EmailingCampaignMessage;
use App\Platform\Permissions;
use App\Repository\Community\EmailingCampaignRepository;
use App\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/emailing')]
class StatsController extends AbstractController
{
    private EmailingCampaignRepository $repository;

    public function __construct(EmailingCampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/{uuid}/stats', name: 'console_community_emailing_stats', methods: ['GET'])]
    public function stats(EmailingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_STATS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        return new JsonResponse($this->repository->findStats($campaign));
    }

    #[Route('/{uuid}/report', name: 'console_community_emailing_stats_report', methods: ['GET'])]
    public function report(EmailingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_STATS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        return $this->render('console/project/community/emailing/report.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{uuid}/report/search', name: 'console_community_emailing_stats_report_search')]
    public function search(EmailingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_VIEW, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        /** @var EmailingCampaignMessage[][] $results */
        $results = $this->repository->searchReport($campaign, Json::decode($request->getContent()));

        $data = [];
        foreach ($results['page'] as $row) {
            $contact = $row['message']->getContact();

            $data[] = [
                'id' => $contact->getUuid()->toRfc4122(),
                'url' => $this->generateUrl('console_community_contacts_view', [
                    'projectUuid' => $this->getProject()->getUuid()->toRfc4122(),
                    'uuid' => $contact->getUuid()->toRfc4122(),
                ]),
                'type' => !$contact->getAccountPassword() ? 'c' : 'm',
                'email' => $contact->getEmail(),
                'hash' => $contact->getEmailHash(),
                'firstName' => $contact->getProfileFirstName(),
                'lastName' => $contact->getProfileLastName(),
                'subscribed' => $contact->hasSettingsReceiveNewsletters(),
                'location' => $contact->getArea()?->getName(),
                'tags' => $contact->getMetadataTagsNames(),
                'opens' => $row['opens'],
                'clicks' => $row['clicks'],
            ];
        }

        return new JsonResponse([
            'total' => $results['total'],
            'contacts' => $data,
        ]);
    }

    #[Route('/{uuid}/report/export', name: 'console_community_emailing_stats_report_export', methods: ['GET'])]
    public function export(EmailingCampaignExporter $exporter, EmailingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_STATS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $exporter->requestExport($this->getUser(), $campaign);

        $this->addFlash('success', 'export.success');

        return $this->redirectToRoute('console_community_emailing_stats_report', [
            'projectUuid' => $campaign->getProject()->getUuid(),
            'uuid' => $campaign->getUuid(),
        ]);
    }
}
