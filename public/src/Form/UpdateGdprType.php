<?php

namespace App\Form;

use App\Form\Model\UpdateGdprData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('settingsReceiveCalls', CheckboxType::class, ['required' => false])
            ->add('settingsByProject', CollectionType::class, [
                'label' => false,
                'entry_type' => UpdateGdprProjectType::class,
            ])
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
