<?php

namespace App\Form\Organization;

use App\Form\Organization\Model\MemberPermissionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberPermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isAdmin', CheckboxType::class, ['required' => false])
            ->add('labels', HiddenType::class, ['required' => false])
            ->add('projectsPermissions', HiddenType::class)
            ->add('projectsPermissionsCategories', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MemberPermissionData::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if (false == $data->isAdmin) {
                    return ['Default', 'permission'];
                }

                return ['Default'];
            },
        ]);
    }
}
