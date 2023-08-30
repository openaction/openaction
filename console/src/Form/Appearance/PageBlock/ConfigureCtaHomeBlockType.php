<?php

namespace App\Form\Appearance\PageBlock;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigureCtaHomeBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('primary', ConfigureCtaHomeBlockButtonType::class)
            ->add('secondary', ConfigureCtaHomeBlockButtonType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project');
        $resolver->setAllowedTypes('project', Project::class);
    }
}
