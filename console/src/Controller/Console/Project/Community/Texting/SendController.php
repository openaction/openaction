<?php

namespace App\Controller\Console\Project\Community\Texting;

use App\Community\ContactViewBuilder;
use App\Community\TextingCampaignSender;
use App\Controller\AbstractController;
use App\Entity\Community\TextingCampaign;
use App\Platform\Permissions;
use App\Util\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route('/console/project/{projectUuid}/community/texting')]
class SendController extends AbstractController
{
    private ContactViewBuilder $contactViewBuilder;
    private TextingCampaignSender $campaignSender;
    private EntityManagerInterface $em;

    public function __construct(ContactViewBuilder $cvb, TextingCampaignSender $cs, EntityManagerInterface $em)
    {
        $this->contactViewBuilder = $cvb;
        $this->campaignSender = $cs;
        $this->em = $em;
    }

    #[Route('/{uuid}/filter-preview', name: 'console_community_texting_filter_preview', methods: ['GET'])]
    public function filterPreviewCount(TextingCampaign $campaign, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $phones = array_filter(explode(' ', trim($request->query->get('contacts'))));
        array_map(static fn ($phone) => strtolower($phone), $phones);

        return new JsonResponse([
            'count' => $this->contactViewBuilder
                ->onlySmsSubscribers()
                ->havingParsedPhone()
                ->inProject($campaign->getProject())
                ->onlyMembers((bool) $request->query->get('member'))
                ->inAreas(array_filter(explode(' ', trim($request->query->get('areas')))))
                ->withTags(
                    array_filter(explode(' ', trim($request->query->get('tags')))),
                    trim($request->query->get('tagsType', 'or')),
                )
                ->withPhones($phones)
                ->count(),
        ]);
    }

    #[Route('/{uuid}/send-test', name: 'console_community_texting_send_test')]
    public function sendTest(TextingCampaign $campaign, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $form = $this->createFormBuilder()
            ->add('phone', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank(), new AssertPhoneNumber(['defaultRegion' => 'FR'])],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sent = $this->campaignSender->sendPreview(
                $campaign,
                PhoneNumber::parse($form->getData()['phone'], 'FR')
            );

            if ($sent) {
                $this->addFlash('success', 'texting.test_sent_success');
            } else {
                $this->addFlash('error', 'subscription.not_enough_credits');
            }

            return $this->redirectToRoute('console_community_texting', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/texting/sendTest.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{uuid}/send-all', name: 'console_community_texting_send_all')]
    public function sendAll(TextingCampaign $campaign, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_SEND, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sent = $this->campaignSender->sendAll($campaign);

            if ($sent) {
                $this->addFlash('success', 'texting.campaign_sent_success');

                $campaign->markSent();

                $this->em->persist($campaign);
                $this->em->flush();
            } else {
                $this->addFlash('error', 'subscription.not_enough_credits');
            }

            return $this->redirectToRoute('console_community_texting', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/community/texting/sendAll.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
            'countFiltered' => $this->contactViewBuilder->forTextingCampaign($campaign)->count(),
        ]);
    }
}
