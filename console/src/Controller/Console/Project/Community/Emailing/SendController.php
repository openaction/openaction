<?php

namespace App\Controller\Console\Project\Community\Emailing;

use App\Community\ContactViewBuilder;
use App\Community\EmailingCampaignSender;
use App\Community\SendgridMailFactory;
use App\Controller\AbstractController;
use App\Entity\Community\EmailingCampaign;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/emailing')]
class SendController extends AbstractController
{
    private ContactViewBuilder $contactViewBuilder;
    private EmailingCampaignSender $campaignSender;
    private EntityManagerInterface $em;

    public function __construct(ContactViewBuilder $cvb, EmailingCampaignSender $cs, EntityManagerInterface $em)
    {
        $this->contactViewBuilder = $cvb;
        $this->campaignSender = $cs;
        $this->em = $em;
    }

    #[Route('/{uuid}/filter-preview', name: 'console_community_emailing_filter_preview', methods: ['GET'])]
    public function filterPreviewCount(EmailingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $emails = array_filter(explode(' ', trim($request->query->get('contacts'))));
        array_walk($emails, static function (&$email) {
            $email = strtolower($email);
        });

        return new JsonResponse([
            'count' => $this->contactViewBuilder
                ->onlyNewsletterSubscribers()
                ->inProject($campaign->getProject())
                ->onlyMembers((bool) $request->query->get('member'))
                ->inAreas(array_filter(explode(' ', trim($request->query->get('areas')))))
                ->withTags(
                    array_filter(explode(' ', trim($request->query->get('tags')))),
                    trim($request->query->get('tagsType', 'or')),
                )
                ->withEmails($emails)
                ->count(),
        ]);
    }

    #[Route('/{uuid}/preview', name: 'console_community_emailing_preview')]
    public function preview(SendgridMailFactory $messageFactory, EmailingCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        return new Response($messageFactory->createCampaignBody($campaign, true));
    }

    #[Route('/{uuid}/send-test', name: 'console_community_emailing_send_test')]
    public function sendTest(EmailingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $form = $this->createFormBuilder(['emails' => $this->getUser()->getEmail()])
            ->add('emails', TextType::class, ['required' => true])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emails = array_filter(array_map('trim', explode(',', $form->getData()['emails'])));

            if ($this->campaignSender->sendPreview($campaign, $emails)) {
                $this->addFlash('success', 'emailings.test_sent_success');
            } else {
                $this->addFlash('error', 'subscription.not_enough_credits');
            }

            return $this->redirectToRoute('console_community_emailing', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/emailing/sendTest.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{uuid}/send-all', name: 'console_community_emailing_send_all')]
    public function sendAll(EmailingCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_EMAIL_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->campaignSender->sendAll($campaign)) {
                $this->addFlash('success', 'emailings.campaign_sent_success');

                $campaign->markSent();

                $this->em->persist($campaign);
                $this->em->flush();
            } else {
                $this->addFlash('error', 'subscription.not_enough_credits');
            }

            return $this->redirectToRoute('console_community_emailing', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/emailing/sendAll.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
            'countFiltered' => $this->contactViewBuilder->forEmailingCampaign($campaign)->count(),
        ]);
    }
}
