<?php

namespace App\Form\Project;

use App\Entity\Model\ProjectMembershipFormSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateMembershipSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldOptions = [
            'translation_domain' => 'project_settings',
            'choices' => ProjectMembershipFormSettings::getAvailableChoices(),
            'choice_label' => fn ($choice) => $choice,
        ];

        $unmappedFieldOptions = array_merge($fieldOptions, [
            'mapped' => false,
            'disabled' => true,
            'empty_data' => ProjectMembershipFormSettings::ASK_ON_REGISTRATION_AS_REQUIRED,
        ]);

        $builder
            ->add('introduction', TextareaType::class, ['required' => false])
            ->add('email', ChoiceType::class, $unmappedFieldOptions)
            ->add('password', ChoiceType::class, $unmappedFieldOptions)
            ->add('profileFirstName', ChoiceType::class, $unmappedFieldOptions)
            ->add('profileLastName', ChoiceType::class, $unmappedFieldOptions)
            ->add('profileFormalTitle', ChoiceType::class, $fieldOptions)
            ->add('profileMiddleName', ChoiceType::class, $fieldOptions)
            ->add('profileBirthdate', ChoiceType::class, $fieldOptions)
            ->add('profileGender', ChoiceType::class, $fieldOptions)
            ->add('profileNationality', ChoiceType::class, $fieldOptions)
            ->add('profileCompany', ChoiceType::class, $fieldOptions)
            ->add('profileJobTitle', ChoiceType::class, $fieldOptions)
            ->add('contactPhone', ChoiceType::class, $fieldOptions)
            ->add('contactWorkPhone', ChoiceType::class, $fieldOptions)
            ->add('socialFacebook', ChoiceType::class, $fieldOptions)
            ->add('socialTwitter', ChoiceType::class, $fieldOptions)
            ->add('socialLinkedIn', ChoiceType::class, $fieldOptions)
            ->add('socialTelegram', ChoiceType::class, $fieldOptions)
            ->add('socialWhatsapp', ChoiceType::class, $fieldOptions)
            ->add('addressStreetLine1', ChoiceType::class, $fieldOptions)
            ->add('addressStreetLine2', ChoiceType::class, $fieldOptions)
            ->add('addressZipCode', ChoiceType::class, $fieldOptions)
            ->add('addressCity', ChoiceType::class, $fieldOptions)
            ->add('addressCountry', ChoiceType::class, $fieldOptions)
            ->add('settingsReceiveNewsletters', ChoiceType::class, $fieldOptions)
            ->add('settingsReceiveSms', ChoiceType::class, $fieldOptions)
            ->add('settingsReceiveCalls', ChoiceType::class, $fieldOptions)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectMembershipFormSettings::class,
        ]);
    }
}
