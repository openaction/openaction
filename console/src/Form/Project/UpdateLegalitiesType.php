<?php

namespace App\Form\Project;

use App\Form\Project\Model\UpdateLegalitiesData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateLegalitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('legalGdprName', TextType::class, ['required' => true])
            ->add('legalGdprEmail', EmailType::class, ['required' => true])
            ->add('legalGdprAddress', TextType::class, ['required' => true])
            ->add('legalPublisherName', TextType::class, ['required' => true])
            ->add('legalPublisherRole', TextType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateLegalitiesData::class,
        ]);
    }
}
