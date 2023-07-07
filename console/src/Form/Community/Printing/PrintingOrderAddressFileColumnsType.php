<?php

namespace App\Form\Community\Printing;

use App\Form\Community\Printing\Model\PrintingOrderAddressFileColumnsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintingOrderAddressFileColumnsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('columnsTypes', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => [
                        'ignored',
                        'formalTitle',
                        'firstName',
                        'lastName',
                        'street1',
                        'street2',
                        'zipCode',
                        'city',
                        'country',
                    ],
                    'choice_label' => static function ($choice) {
                        return 'edit.delivery.addressed.columns.types.'.$choice;
                    },
                    'translation_domain' => 'project_printing',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrintingOrderAddressFileColumnsData::class,
        ]);
    }
}
