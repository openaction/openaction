<?php

namespace App\Form\Website;

use App\Form\Website\Model\PetitionLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionLocalizedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', HiddenType::class, ['required' => false])
            ->add('description', HiddenType::class, ['required' => false])
            ->add('content', HiddenType::class, ['required' => false])
            ->add('categories', HiddenType::class, ['required' => false])
            ->add('onlyForMembers', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PetitionLocalizedData::class,
            'validation_groups' => ['Default'],
            'csrf_protection' => false,
        ]);
    }
}