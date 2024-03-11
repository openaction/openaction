<?php

namespace App\Form\Project;

use App\Form\Project\Model\UpdateSocialAccountsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateSocialAccountsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['required' => false])
            ->add('phone', TextType::class, ['required' => false])
            ->add('facebook', UrlType::class, ['required' => false])
            ->add('twitter', UrlType::class, ['required' => false])
            ->add('instagram', UrlType::class, ['required' => false])
            ->add('linkedIn', UrlType::class, ['required' => false])
            ->add('youtube', UrlType::class, ['required' => false])
            ->add('medium', UrlType::class, ['required' => false])
            ->add('telegram', TextType::class, ['required' => false])
            ->add('snapchat', TextType::class, ['required' => false])
            ->add('whatsapp', UrlType::class, ['required' => false])
            ->add('tiktok', UrlType::class, ['required' => false])
            ->add('threads', UrlType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateSocialAccountsData::class,
        ]);
    }
}
