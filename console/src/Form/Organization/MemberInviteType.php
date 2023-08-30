<?php

namespace App\Form\Organization;

use App\Form\Organization\Model\MemberInviteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberInviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('isAdmin', CheckboxType::class, [
                'required' => false,
            ])
            ->add('locale', ChoiceType::class, [
                'choices' => [
                    'English' => 'en',
                    'Français' => 'fr',
                    'Deutsch' => 'de',
                ],
            ])
            ->add('projectsPermissions', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MemberInviteData::class,
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
