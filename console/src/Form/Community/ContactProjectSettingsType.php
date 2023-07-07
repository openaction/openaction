<?php

namespace App\Form\Community;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactProjectSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('settingsReceiveNewsletters', CheckboxType::class, ['required' => false])
            ->add('settingsReceiveSms', CheckboxType::class, ['required' => false])
            ->add('settingsReceiveCalls', CheckboxType::class, ['required' => false])
        ;
    }
}
