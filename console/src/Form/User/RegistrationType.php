<?php

namespace App\Form\User;

use App\Form\User\Model\RegistrationRequestData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public const HONEYPOT = 'email';
    public const EMAIL = 'name';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::HONEYPOT)
            ->add(self::EMAIL)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationRequestData::class,
        ]);
    }
}
