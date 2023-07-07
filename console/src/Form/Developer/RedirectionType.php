<?php

namespace App\Form\Developer;

use App\Form\Developer\Model\RedirectionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RedirectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', TextType::class, ['required' => true])
            ->add('target', TextType::class, ['required' => true])
            ->add('code', ChoiceType::class, [
                'choices' => [
                    'HTTP 301 Moved Permanently' => 301,
                    'HTTP 302 Found' => 302,
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RedirectionData::class,
        ]);
    }
}
