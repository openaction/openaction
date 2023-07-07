<?php

namespace App\Form\Appearance\PageBlock;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ConfigureCtaHomeBlockButtonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('label', TextType::class, ['required' => false, 'constraints' => [new Length(['max' => 100])]])
            ->add('target', TextType::class, ['required' => false])
            ->add('openNewTab', CheckboxType::class, ['required' => false])
        ;
    }
}
