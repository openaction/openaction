<?php

namespace App\Form\Admin;

use App\Form\Admin\Model\StartOnPremiseData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StartOnPremiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('region', TextType::class, ['required' => true])
            ->add('websiteName', TextType::class, ['required' => true])
            ->add('websiteDescription', TextareaType::class, ['required' => true])
            ->add('subdomain', TextType::class, ['required' => true])
            ->add('adminEmail', EmailType::class, ['required' => true])

            ->add('facebook', UrlType::class, ['required' => false])
            ->add('twitter', UrlType::class, ['required' => false])
            ->add('instagram', UrlType::class, ['required' => false])
            ->add('linkedIn', UrlType::class, ['required' => false])
            ->add('youtube', UrlType::class, ['required' => false])
            ->add('medium', UrlType::class, ['required' => false])
            ->add('telegram', TextType::class, ['required' => false])
            ->add('snapchat', TextType::class, ['required' => false])

            ->add('mainImage', FileType::class, ['required' => false])
            ->add('favicon', FileType::class, ['required' => false])
            ->add('shareImage', FileType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StartOnPremiseData::class,
        ]);
    }
}
