<?php

namespace App\Form\Community;

use App\Entity\Organization;
use App\Form\Community\Model\ImportMetadataData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportMetadataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typesChoices = [
            'ignored',
            'email',
            'profileFormalTitle',
            'profileFirstName',
            'profileMiddleName',
            'profileLastName',
            'profileBirthdate',
            'profileGender',
            'profileCompany',
            'profileJobTitle',
            'contactPhone',
            'contactWorkPhone',
            'socialFacebook',
            'socialTwitter',
            'socialLinkedIn',
            'socialTelegram',
            'socialWhatsapp',
            'addressStreetLine1',
            'addressStreetLine2',
            'addressZipCode',
            'addressCity',
            'addressCountry',
            'settingsReceiveNewsletters',
            'settingsReceiveSms',
            'settingsReceiveCalls',
            'metadataComment',
            'metadataTagsList',
            'metadataTag',
        ];

        $builder
            ->add('columnsTypes', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => $typesChoices,
                    'choice_label' => static function ($choice) {
                        return 'contacts.import.columns.types.'.$choice;
                    },
                    'translation_domain' => 'organization_community',
                ],
            ])
            ->add('areaId', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('organization');
        $resolver->setAllowedTypes('organization', Organization::class);

        $resolver->setDefaults([
            'data_class' => ImportMetadataData::class,
        ]);
    }
}
