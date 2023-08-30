<?php

namespace App\Form\Project;

use App\Entity\Model\SocialSharers;
use App\Form\Project\Model\UpdateSocialSharersData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateSocialSharersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sharers', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => SocialSharers::getAllSocialSharers(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateSocialSharersData::class,
        ]);
    }
}
