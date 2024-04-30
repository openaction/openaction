<?php

namespace App\Form\Website;

use App\Form\Website\Model\PetitionLocalizedLocaleData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionLocalizedLocaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('locale', ChoiceType::class, [
            'translation_domain' => 'project_petitions',
            'choices' => [
                'create.localized.en' => 'gb',
                'create.localized.fr' => 'fr',
                'create.localized.de' => 'de',
                'create.localized.it' => 'it',
                'create.localized.nl' => 'nl',
                'create.localized.pt' => 'pt',
            ],
        ])
        ->add('petitionUuid', HiddenType::class)
        ->setMethod('GET');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PetitionLocalizedLocaleData::class,
            'csrf_protection' => false,
        ]);
    }
}
