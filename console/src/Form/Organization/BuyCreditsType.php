<?php

namespace App\Form\Organization;

use App\Form\CountryType;
use App\Form\Organization\Model\BuyCreditsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuyCreditsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', NumberType::class, ['required' => true])
            ->add('billingOrganization', TextType::class, ['required' => true])
            ->add('billingEmail', EmailType::class, ['required' => true])
            ->add('billingStreetLine1', TextType::class, ['required' => true])
            ->add('billingStreetLine2', TextType::class, ['required' => false])
            ->add('billingPostalCode', TextType::class, ['required' => true])
            ->add('billingCity', TextType::class, ['required' => true])
            ->add('billingCountry', CountryType::class, ['required' => true])
            ->add('recipientFirstName', TextType::class, ['required' => true])
            ->add('recipientLastName', TextType::class, ['required' => true])
            ->add('recipientEmail', EmailType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuyCreditsData::class,
        ]);
    }
}
