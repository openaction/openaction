<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateGdprProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('settingsReceiveNewsletters', CheckboxType::class, ['required' => false, 'label' => 'gdpr.newsletter'])
            ->add('settingsReceiveSms', CheckboxType::class, ['required' => false, 'label' => 'gdpr.sms'])
            ->add('settingsReceiveCalls', CheckboxType::class, ['required' => false, 'label' => 'gdpr.calls'])
        ;
    }
}
