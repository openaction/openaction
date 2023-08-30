<?php

namespace App\Form\Community;

use App\Entity\Community\PhoningCampaign;
use App\Form\Community\Model\PhoningCampaignMetaData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoningCampaignMetaDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('endAfter', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'choices' => PhoningCampaign::getEndAfterRanges(),
                'choice_label' => static function ($choice) {
                    return 'metadata.details.endAfter.choice.'.$choice;
                },
                'translation_domain' => 'project_phoning',
            ])
            ->add('onlyForMembers', HiddenType::class, ['required' => false])
            ->add('tagsFilter', HiddenType::class, ['required' => false])
            ->add('tagsFilterType', HiddenType::class, ['required' => false])
            ->add('areasFilterIds', HiddenType::class, ['required' => false])
            ->add('contactsFilter', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhoningCampaignMetaData::class,
        ]);
    }
}
