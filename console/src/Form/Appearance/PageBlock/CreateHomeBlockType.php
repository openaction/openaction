<?php

namespace App\Form\Appearance\PageBlock;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateHomeBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => array_combine($options['types'], $options['types']),
                'constraints' => [new NotBlank(), new Choice(['choices' => $options['types']])],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('types');
        $resolver->setAllowedTypes('types', 'array');
    }
}
