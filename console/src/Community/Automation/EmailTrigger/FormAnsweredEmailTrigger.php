<?php

namespace App\Community\Automation\EmailTrigger;

use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use App\Entity\Website\FormAnswer;

class FormAnsweredEmailTrigger extends AbstractEmailTrigger
{
    public function getTrigger(): string
    {
        return EmailAutomation::TRIGGER_NEW_FORM_ANSWER;
    }

    /**
     * @param FormAnswer $formAnswer
     */
    public function handle(Organization $organization, ?Contact $contact, $formAnswer)
    {
        $vars = [
            '-form-project-' => $formAnswer->getForm()->getProject()->getUuid()->toRfc4122(),
            '-form-id-' => $formAnswer->getForm()->getUuid()->toRfc4122(),
            '-form-title-' => $formAnswer->getForm()->getTitle(),
        ];

        $i = 1;
        foreach ($formAnswer->getAnswers() as $answer) {
            $vars['-form-answer-'.$i.'-'] = $answer;
            ++$i;
        }

        $automations = $this->findMatchingAutomationsFor(EmailAutomation::TRIGGER_NEW_FORM_ANSWER, $organization, $contact);

        foreach ($automations as $automation) {
            if (!$automation->getFormFilter() || $automation->getFormFilter()->getId() === $formAnswer->getForm()->getId()) {
                $this->sendAutomationEmail($automation, $contact, $vars);
            }
        }
    }
}
