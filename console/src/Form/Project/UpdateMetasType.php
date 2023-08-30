<?php

namespace App\Form\Project;

use App\Form\Project\Model\UpdateMetasData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateMetasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('websiteSharer', FileType::class, ['required' => false])
            ->add('websiteMetaTitle', TextType::class, ['required' => false])
            ->add('websiteMetaDescription', TextareaType::class, ['required' => false, 'attr' => ['rows' => 2]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateMetasData::class,
        ]);
    }
}
