<?php

namespace App\Form\User;

use App\Entity\Model\NotificationSettings;
use App\Form\User\Model\NotificationSettingsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('events', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => NotificationSettings::getAllEvents(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotificationSettingsData::class,
        ]);
    }
}
