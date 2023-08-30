<?php

namespace App\Form\Member;

use App\Form\Member\Model\UpdateGdprData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateGdprType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('settingsReceiveNewsletters', CheckboxType::class, ['required' => false])
            ->add('settingsReceiveSms', CheckboxType::class, ['required' => false])
            ->add('alwaysSend', HiddenType::class, [
                'empty_data' => 'always send even if no checkbox is checked',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateGdprData::class,
        ]);
    }
}
