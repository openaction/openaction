<?php

namespace App\Form\Member;

use App\Form\CountryType;
use App\Form\Member\Model\JoinData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Required fields
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, ['type' => PasswordType::class])
            ->add('profileFirstName', TextType::class)
            ->add('profileLastName', TextType::class)

            // Details
            ->add('profileFormalTitle', ChoiceType::class, [
                'empty_data' => '',
                'choices' => [
                    '',
                    'membership.join.choices.profileFormalTitle.mr',
                    'membership.join.choices.profileFormalTitle.ms',
                ],
                'choice_label' => fn ($choice) => $choice,
                'translation_domain' => 'messages',
            ])
            ->add('profileMiddleName', TextType::class)
            ->add('profileBirthdate', BirthdayType::class)
            ->add('profileGender', ChoiceType::class, [
                'empty_data' => '',
                'choices' => [
                    '',
                    'membership.join.choices.profileGender.male',
                    'membership.join.choices.profileGender.female',
                    'membership.join.choices.profileGender.transgender',
                    'membership.join.choices.profileGender.non_binary',
                    'membership.join.choices.profileGender.other',
                ],
                'choice_label' => fn ($choice) => $choice,
                'translation_domain' => 'messages',
            ])
            ->add('profileNationality', CountryType::class, ['required' => true])
            ->add('profileCompany', TextType::class)
            ->add('profileJobTitle', TextType::class)

            // Contact
            ->add('contactPhone', TextType::class)
            ->add('contactWorkPhone', TextType::class)
            ->add('socialFacebook', TextType::class)
            ->add('socialTwitter', TextType::class)
            ->add('socialLinkedIn', TextType::class)
            ->add('socialTelegram', TextType::class)
            ->add('socialWhatsapp', TextType::class)

            // Address
            ->add('addressStreetLine1', TextType::class)
            ->add('addressStreetLine2', TextType::class)
            ->add('addressZipCode', TextType::class)
            ->add('addressCity', TextType::class)
            ->add('addressCountry', CountryType::class)

            // Notifications
            ->add('settingsReceiveNewsletters', CheckboxType::class)
            ->add('settingsReceiveSms', CheckboxType::class)
            ->add('settingsReceiveCalls', CheckboxType::class)
            ->add('acceptPolicy', CheckboxType::class, ['required' => true])
        ;

        foreach ($options['membership_settings'] as $field => $behavior) {
            switch ($behavior) {
                case 'membership.form.rule.required':
                    $builder->get($field)->setRequired(true);
                    break;

                case 'membership.form.rule.optional':
                    $builder->get($field)->setRequired(false);
                    break;

                default:
                    $builder->remove($field);
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('membership_settings');
        $resolver->setAllowedTypes('membership_settings', 'array');

        $resolver->setDefaults([
            'data_class' => JoinData::class,
        ]);
    }
}
