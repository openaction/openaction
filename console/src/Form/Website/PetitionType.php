<?php

namespace App\Form\Website;

use App\Form\Website\Model\PetitionLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', HiddenType::class, ['required' => false])
            ->add('endAt', HiddenType::class, ['required' => false])
            ->add('signatureGoal', HiddenType::class, ['required' => false])
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
