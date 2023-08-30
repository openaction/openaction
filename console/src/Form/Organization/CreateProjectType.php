<?php

namespace App\Form\Organization;

use App\Form\Organization\Model\CreateProjectData;
use App\Platform\Features;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', HiddenType::class)
            ->add('areasIds', HiddenType::class)
            ->add('tags', HiddenType::class)
            ->add('modules', ChoiceType::class, [
                'multiple' => true,
                'choices' => Features::allModules(),
                'choice_label' => fn ($choice) => $choice,
            ])
            ->add('tools', ChoiceType::class, [
                'multiple' => true,
                'choices' => Features::allTools(),
                'choice_label' => fn ($choice) => $choice,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateProjectData::class,
        ]);
    }
}
