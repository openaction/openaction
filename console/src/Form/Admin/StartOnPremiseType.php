<?php

namespace App\Form\Admin;

use App\Form\Admin\Model\StartOnPremiseData;
use App\Platform\Circonscriptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StartOnPremiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('circonscription', ChoiceType::class, ['required' => true, 'choices' => Circonscriptions::getFrChoices()])
            ->add('politicalParty', TextType::class, ['required' => true])
            ->add('candidateName', TextType::class, ['required' => true])
            ->add('subdomain', TextType::class, ['required' => true])
            ->add('adminEmail', EmailType::class, ['required' => true])
            ->add('billingName', TextType::class, ['required' => true])
            ->add('billingEmail', EmailType::class, ['required' => true])
            ->add('billingAddressStreetLine1', TextType::class, ['required' => true])
            ->add('billingAddressStreetLine2', TextType::class, ['required' => false])
            ->add('billingAddressPostalCode', TextType::class, ['required' => true])
            ->add('billingAddressCity', TextType::class, ['required' => true])
            ->add('billingAddressCountry', CountryType::class, ['required' => true])
            ->add('enableWebsite', CheckboxType::class, ['required' => false])
            ->add('enableLocalPosts', CheckboxType::class, ['required' => false])
            ->add('enableDonation', CheckboxType::class, ['required' => false])
            ->add('enablePrint', CheckboxType::class, ['required' => false])
            ->add('mainImage', FileType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StartOnPremiseData::class,
        ]);
    }
}
