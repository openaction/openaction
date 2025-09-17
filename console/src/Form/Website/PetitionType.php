<?php

namespace App\Form\Website;

use App\Form\Website\Model\PetitionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slug', HiddenType::class, ['required' => false])
            ->add('startAt', HiddenType::class, ['required' => false])
            ->add('endAt', HiddenType::class, ['required' => false])
            ->add('signaturesGoal', HiddenType::class, ['required' => false])
            ->add('authors', HiddenType::class, ['required' => false])
            ->add('publishedAt', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PetitionData::class,
            'validation_groups' => ['Default'],
            'csrf_protection' => false,
        ]);
    }
}
