<?php

namespace App\Form\Admin;

use App\Form\Admin\Model\StartTrialData;
use App\Platform\Plans;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StartTrialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('plan', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Essential' => Plans::ESSENTIAL,
                    'Standard' => Plans::STANDARD,
                    'Premium' => Plans::PREMIUM,
                    'Organization' => Plans::ORGANIZATION,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StartTrialData::class,
        ]);
    }
}
