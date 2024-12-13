<?php

namespace App\Form;

use App\Form\Model\SubscribeNewsletterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscribeNewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['required' => true])
            ->add('firstName', TextType::class, ['required' => false])
            ->add('lastName', TextType::class, ['required' => false])
            ->add('phone', TextType::class, ['required' => false])
            ->add('country', CountryType::class, ['required' => false])
            ->add('zipCode', TextType::class, ['required' => false])
        ;

        if ($options['enable_gdpr_fields']) {
            $builder->add('acceptPolicy', CheckboxType::class, ['required' => true, 'mapped' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubscribeNewsletterData::class,
            'csrf_protection' => false,
            'enable_gdpr_fields' => true,
        ]);

        $resolver->setAllowedTypes('enable_gdpr_fields', 'bool');
    }
}
