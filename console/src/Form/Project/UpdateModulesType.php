<?php

namespace App\Form\Project;

use App\Form\Project\Model\UpdateModulesData;
use App\Platform\Features;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateModulesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
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
            'data_class' => UpdateModulesData::class,
        ]);
    }
}
