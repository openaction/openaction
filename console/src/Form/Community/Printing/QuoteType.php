<?php

namespace App\Form\Community\Printing;

use App\Form\Community\Printing\Model\QuoteData;
use App\Form\CountryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantities', QuoteQuantitiesType::class, [
                'required' => true,
                'products' => $options['products'],
            ])
            ->add('deliveryStreet1', TextType::class, ['required' => true])
            ->add('deliveryStreet2', TextType::class, ['required' => false])
            ->add('deliveryZipCode', TextType::class, ['required' => true])
            ->add('deliveryCity', TextType::class, ['required' => true])
            ->add('deliveryCountry', CountryType::class, ['required' => true])
            ->add('billingOrganization', TextType::class, ['required' => true])
            ->add('billingEmail', EmailType::class, ['required' => true])
            ->add('billingStreet1', TextType::class, ['required' => true])
            ->add('billingStreet1', TextType::class, ['required' => true])
            ->add('billingStreet2', TextType::class, ['required' => false])
            ->add('billingZipCode', TextType::class, ['required' => true])
            ->add('billingCity', TextType::class, ['required' => true])
            ->add('billingCountry', CountryType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('products');
        $resolver->setAllowedTypes('products', 'array');

        $resolver->setDefaults([
            'data_class' => QuoteData::class,
        ]);
    }
}
