<?php

namespace App\Api\Persister;

use App\Api\Model\FormAnswerApiData;
use App\Api\Model\PhoningCampaignCallApiData;
use App\Entity\Community\Contact;
use App\Entity\Community\PhoningCampaignCall;
use App\Entity\Community\PhoningCampaignTarget;
use App\Entity\Community\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhoningCampaignCallApiPersister
{
    public function __construct(
        private EntityManagerInterface $em,
        private FormAnswerApiPersister $formAnswerPersister,
        private SluggerInterface $slugger,
    ) {
    }

    public function startCall(PhoningCampaignTarget $target, Contact $author): PhoningCampaignCall
    {
        $call = new PhoningCampaignCall($target, $author);

        $this->em->persist($call);
        $this->em->flush();

        return $call;
    }

    public function saveCall(PhoningCampaignCall $call, PhoningCampaignCallApiData $data)
    {
        $contact = $call->getTarget()->getContact();
        $form = $call->getTarget()->getCampaign()->getForm();

        $call->setStatus($data->status);
        $this->em->persist($call);
        $this->em->flush();

        // If the phone number is valid but should be called again later, mark it as such
        if (PhoningCampaignCall::STATUS_FAILED_NO_ANSWER === $data->status
            || PhoningCampaignCall::STATUS_FAILED_CALL_LATER === $data->status) {
            $this->em->getRepository(Tag::class)->addContactTagByName(
                $contact,
                'phoning-retry-'.$this->slugger->slug($call->getTarget()->getCampaign()->getName())->lower()
            );

            return;
        }

        // If an answer was provided and the status is success, persist it
        if (PhoningCampaignCall::STATUS_ACCEPTED === $data->status && $data->answers) {
            $formAnswerData = new FormAnswerApiData();
            $formAnswerData->fields = $data->answers;

            $call->getTarget()->setAnswer($this->formAnswerPersister->persist($form, $formAnswerData, $contact));

        // If the contact requested to be unsubscribed from calls
        } elseif (PhoningCampaignCall::STATUS_FAILED_NO_CALL === $data->status
            || PhoningCampaignCall::STATUS_FAILED_UNREGISTER === $data->status) {
            $contact->updateCallsSubscription(false, 'phoning:unregister');
        }

        $this->em->persist($call->getTarget());
        $this->em->persist($contact);
        $this->em->flush();
    }
}
