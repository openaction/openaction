<?php

namespace App\Form\Billing;

use App\Form\Billing\Model\UpdateBillingDetailsData;
use App\Form\CountryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBillingDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('streetLine1', TextType::class, ['required' => true])
            ->add('streetLine2', TextType::class, ['required' => false])
            ->add('postalCode', TextType::class, ['required' => true])
            ->add('city', TextType::class, ['required' => true])
            ->add('country', CountryType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateBillingDetailsData::class,
        ]);
    }
}
