<?php

namespace App\Form\Website;

use App\Form\Website\Model\TrombinoscopePersonData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrombinoscopePersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', HiddenType::class)
            ->add('role', HiddenType::class)
            ->add('description', HiddenType::class)
            ->add('content', HiddenType::class)
            ->add('publishedAt', HiddenType::class)
            ->add('socialWebsite', HiddenType::class)
            ->add('socialEmail', HiddenType::class)
            ->add('socialFacebook', HiddenType::class)
            ->add('socialTwitter', HiddenType::class)
            ->add('socialInstagram', HiddenType::class)
            ->add('socialLinkedIn', HiddenType::class)
            ->add('socialYoutube', HiddenType::class)
            ->add('socialMedium', HiddenType::class)
            ->add('socialTelegram', HiddenType::class)
            ->add('categories', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrombinoscopePersonData::class,
            'validation_groups' => ['Default'],
            'csrf_protection' => false,
        ]);
    }
}
