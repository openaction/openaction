<?php

namespace App\Form\Community;

use App\Entity\Area;
use App\Entity\Community\Contact;
use App\Form\Community\Model\ContactData;
use App\Form\CountryType;
use App\Repository\AreaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['required' => false])
            ->add('additionalEmails', CollectionType::class, [
                'entry_type' => EmailType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('picture', FileType::class, ['required' => false])
            ->add('profileFormalTitle', TextType::class, ['required' => false])
            ->add('profileFirstName', TextType::class, ['required' => false])
            ->add('profileMiddleName', TextType::class, ['required' => false])
            ->add('profileLastName', TextType::class, ['required' => false])
            ->add('profileBirthdate', BirthdayType::class, ['required' => false, 'widget' => 'single_text'])
            ->add('profileGender', ChoiceType::class, [
                'choices' => Contact::GENDERS,
                'choice_label' => fn ($choice) => 'gender.'.$choice,
                'translation_domain' => 'global',
                'required' => false,
            ])
            ->add('profileNationality', CountryType::class, ['required' => false])
            ->add('profileCompany', TextType::class, ['required' => false])
            ->add('profileJobTitle', TextType::class, ['required' => false])
            ->add('contactPhone', TextType::class, ['required' => false])
            ->add('contactWorkPhone', TextType::class, ['required' => false])
            ->add('socialFacebook', TextType::class, ['required' => false])
            ->add('socialTwitter', TextType::class, ['required' => false])
            ->add('socialLinkedIn', TextType::class, ['required' => false])
            ->add('socialTelegram', TextType::class, ['required' => false])
            ->add('socialWhatsapp', TextType::class, ['required' => false])
            ->add('addressStreetLine1', TextType::class, ['required' => false])
            ->add('addressStreetLine2', TextType::class, ['required' => false])
            ->add('addressCity', TextType::class, ['required' => false])
            ->add('metadataComment', TextareaType::class, ['required' => false, 'attr' => ['rows' => 5]])
        ;

        if ($options['allow_edit_tags']) {
            $builder->add('metadataTags', HiddenType::class, ['required' => false]);
        }

        if ($options['allow_edit_area']) {
            $builder
                ->add('addressZipCode', TextType::class, ['required' => false])
                ->add('addressCountry', EntityType::class, [
                    'class' => Area::class,
                    'required' => false,
                    'query_builder' => static function (AreaRepository $repo) {
                        return $repo->createQueryBuilder('a')
                            ->where('a.type = :type')
                            ->setParameter('type', Area::TYPE_COUNTRY)
                            ->orderBy('a.name', 'ASC')
                        ;
                    },
                ])
            ;
        }

        if ($options['allow_edit_settings']) {
            $builder
                ->add('settingsReceiveNewsletters', CheckboxType::class, ['required' => false])
                ->add('settingsReceiveSms', CheckboxType::class, ['required' => false])
                ->add('settingsReceiveCalls', CheckboxType::class, ['required' => false])
                ->add('settingsByProject', CollectionType::class, [
                    'label' => false,
                    'entry_type' => ContactProjectSettingsType::class,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_edit_tags' => true,
            'allow_edit_area' => true,
            'allow_edit_settings' => true,
            'data_class' => ContactData::class,
        ]);
    }
}
