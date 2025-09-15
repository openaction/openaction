<?php

namespace App\Controller\Membership;

use App\Client\CitipoInterface;
use App\FormBuilder\SymfonyFormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members/phoning/{id}")
 */
class PhoningController extends AbstractMembershipController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("", name="membership_phoning_start")
     */
    public function start(Request $request, string $id)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        if (!$campaign = $this->citipo->getPhoningCampaign($this->getApiToken(), $this->getAuthToken($request), $id)) {
            throw $this->createNotFoundException('Campaign not found');
        }

        return $this->render('member/phoning/start.html.twig', [
            'contact' => $contact,
            'campaign' => $campaign,
            'finished' => $request->query->getBoolean('finished'),
        ]);
    }

    /**
     * @Route("/resolve-target", name="membership_phoning_resolve_target")
     */
    public function resolveTarget(Request $request, string $id)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        if (!$call = $this->citipo->resolvePhoningCampaignTarget($this->getApiToken(), $this->getAuthToken($request), $id)) {
            return $this->redirectToRoute('membership_phoning_start', ['id' => $id, 'finished' => '1']);
        }

        return $this->redirectToRoute('membership_phoning_call_phone', ['id' => $id, 'callId' => $call->id]);
    }

    /**
     * @Route("/{callId}/phone", name="membership_phoning_call_phone")
     */
    public function callPhone(Request $request, string $id, string $callId)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        if (!$campaign = $this->citipo->getPhoningCampaign($this->getApiToken(), $this->getAuthToken($request), $id)) {
            throw $this->createNotFoundException('Campaign not found');
        }

        if (!$call = $this->citipo->getPhoningCampaignCall($this->getApiToken(), $this->getAuthToken($request), $id, $callId)) {
            throw $this->createNotFoundException('Call not found');
        }

        return $this->render('member/phoning/callPhone.html.twig', [
            'contact' => $contact,
            'campaign' => $campaign,
            'call' => $call,
        ]);
    }

    /**
     * @Route("/{callId}/details", name="membership_phoning_call_details")
     */
    public function callDetails(Request $request, string $id, string $callId)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        if (!$campaign = $this->citipo->getPhoningCampaign($this->getApiToken(), $this->getAuthToken($request), $id)) {
            throw $this->createNotFoundException('Campaign not found');
        }

        if (!$call = $this->citipo->getPhoningCampaignCall($this->getApiToken(), $this->getAuthToken($request), $id, $callId)) {
            throw $this->createNotFoundException('Call not found');
        }

        return $this->render('member/phoning/callDetails.html.twig', [
            'contact' => $contact,
            'campaign' => $campaign,
            'call' => $call,
        ]);
    }

    /**
     * @Route("/{callId}/answered", name="membership_phoning_call_answered")
     */
    public function callResultForm(SymfonyFormBuilder $builder, Request $request, string $id, string $callId)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$contact = $this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        if (!$campaign = $this->citipo->getPhoningCampaign($this->getApiToken(), $this->getAuthToken($request), $id)) {
            throw $this->createNotFoundException('Campaign not found');
        }

        if (!$call = $this->citipo->getPhoningCampaignCall($this->getApiToken(), $this->getAuthToken($request), $id, $callId)) {
            throw $this->createNotFoundException('Call not found');
        }

        $data = [];
        foreach ($campaign->form->blocks->data as $key => $block) {
            if (!$block->field) {
                continue;
            }

            switch ($block->type) {
                case SymfonyFormBuilder::TYPE_EMAIL: $data['field'.$key] = $call->contact->email ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_FORMAL_TITLE: $data['field'.$key] = $call->contact->profileFormalTitle ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_FIRST_NAME: $data['field'.$key] = $call->contact->profileFirstName ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_MIDDLE_NAME: $data['field'.$key] = $call->contact->profileMiddleName ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_LAST_NAME: $data['field'.$key] = $call->contact->profileLastName ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_BIRTHDATE: $data['field'.$key] = $call->contact->profileBirthdate ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_GENDER: $data['field'.$key] = $call->contact->profileGender ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_NATIONALITY: $data['field'.$key] = $call->contact->profileNationality ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_COMPANY: $data['field'.$key] = $call->contact->profileCompany ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_JOB_TITLE: $data['field'.$key] = $call->contact->profileJobTitle ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_PHONE: $data['field'.$key] = $call->contact->contactPhone ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_WORK_PHONE: $data['field'.$key] = $call->contact->contactWorkPhone ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_SOCIAL_FACEBOOK: $data['field'.$key] = $call->contact->socialFacebook ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_SOCIAL_TWITTER: $data['field'.$key] = $call->contact->socialTwitter ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_SOCIAL_LINKEDIN: $data['field'.$key] = $call->contact->socialLinkedIn ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_SOCIAL_TELEGRAM: $data['field'.$key] = $call->contact->socialTelegram ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_SOCIAL_WHATSAPP: $data['field'.$key] = $call->contact->socialWhatsapp ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_STREET_ADDRESS: $data['field'.$key] = $call->contact->addressStreetLine1 ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_STREET_ADDRESS_2: $data['field'.$key] = $call->contact->addressStreetLine2 ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_CITY: $data['field'.$key] = $call->contact->addressCity ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_ZIP_CODE: $data['field'.$key] = $call->contact->addressZipCode ?: '';
                    break;
                case SymfonyFormBuilder::TYPE_COUNTRY: $data['field'.$key] = $call->contact->addressCountry ?: '';
                    break;
                default: $data['field'.$key] = '';
            }
        }

        $form = $builder->createFromBlocks($campaign->form->blocks->data, $data, false);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answers = $builder->normalizeFormData($campaign->form->blocks->data, $form->getData());

            $this->citipo->persistPhoningCampaignCallResult(
                $this->getApiToken(),
                $this->getAuthToken($request),
                $id,
                $callId,
                'accepted',
                $answers
            );

            return $this->redirectToRoute('membership_phoning_start', ['id' => $id]);
        }

        return $this->render('member/phoning/callResultForm.html.twig', [
            'contact' => $contact,
            'campaign' => $campaign,
            'call' => $call,
            'formData' => $campaign->form,
            'form' => $form->createView(),
            'success' => $request->query->getBoolean('s'),
        ]);
    }

    /**
     * @Route("/{callId}/failed/{type}", name="membership_phoning_call_failed")
     */
    public function callResultFailed(Request $request, string $id, string $callId, string $type)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->authorize($request)) {
            return $this->redirectToRoute('membership_login');
        }

        $this->citipo->persistPhoningCampaignCallResult(
            $this->getApiToken(),
            $this->getAuthToken($request),
            $id,
            $callId,
            'failed_'.$type
        );

        return $this->redirectToRoute('membership_phoning_start', ['id' => $id]);
    }
}
