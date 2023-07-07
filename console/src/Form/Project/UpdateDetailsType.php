<?php

namespace App\Form\Project;

use App\Form\Project\Model\UpdateDetailsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', HiddenType::class)
            ->add('areasIds', HiddenType::class)
            ->add('tags', HiddenType::class)
            ->add('locale', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'English' => 'en',
                    'Français' => 'fr',
                    'Português' => 'pt_BR',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateDetailsData::class,
        ]);
    }
}
