<?php

namespace App\Form\Website;

use App\Form\Website\Model\LocalizedPetitionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizedPetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', HiddenType::class, ['required' => false])
            ->add('content', HiddenType::class, ['required' => false])
            ->add('description', HiddenType::class, ['required' => false])
            ->add('submitButtonLabel', HiddenType::class, ['required' => false])
            ->add('optinLabel', HiddenType::class, ['required' => false])
            ->add('legalities', HiddenType::class, ['required' => false])
            ->add('addressedTo', HiddenType::class, ['required' => false])
            ->add('categories', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalizedPetitionData::class,
            'validation_groups' => ['Default'],
            'csrf_protection' => false,
        ]);
    }
}
