<?php

namespace App\Community;

use App\Bridge\Twilio\TwilioInterface;
use App\Community\Consumer\SendTextingCampaignMessage;
use App\Entity\Community\TextingCampaign;
use App\Repository\OrganizationRepository;
use libphonenumber\PhoneNumber;
use Symfony\Component\Messenger\MessageBusInterface;

class TextingCampaignSender
{
    private OrganizationRepository $organizationRepo;
    private ContactViewBuilder $contactViewBuilder;
    private TwilioInterface $twilio;
    private MessageBusInterface $bus;

    public function __construct(OrganizationRepository $or, ContactViewBuilder $cvb, TwilioInterface $t, MessageBusInterface $bus)
    {
        $this->organizationRepo = $or;
        $this->contactViewBuilder = $cvb;
        $this->twilio = $t;
        $this->bus = $bus;
    }

    public function sendPreview(TextingCampaign $campaign, PhoneNumber $number): bool
    {
        if (!$this->organizationRepo->useTextsCredits($campaign->getProject()->getOrganization(), 1, 'texting_preview')) {
            return false;
        }

        $this->twilio->sendMessage(
            $campaign->getProject()->getOrganization()->getTextingSenderCode(),
            \App\Util\PhoneNumber::format($number),
            $campaign->getContent()
        );

        return true;
    }

    public function sendAll(TextingCampaign $campaign): bool
    {
        $recipientsCount = $this->contactViewBuilder->forTextingCampaign($campaign)->count();

        if (!$this->organizationRepo->useTextsCredits($campaign->getProject()->getOrganization(), $recipientsCount, 'texting')) {
            return false;
        }

        $this->bus->dispatch(new SendTextingCampaignMessage($campaign->getId()));

        return true;
    }
}
