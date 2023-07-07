<?php

namespace App\Form\User;

use App\Form\User\Model\AccountData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('firstName', TextType::class)
          ->add('lastName', TextType::class)
          ->add('locale', ChoiceType::class, [
              'choices' => [
                  'English' => 'en',
                  'Français' => 'fr',
                  'Deutsch' => 'de',
              ],
          ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccountData::class,
        ]);
    }
}
