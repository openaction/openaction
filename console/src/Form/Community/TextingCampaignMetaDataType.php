<?php

namespace App\Form\Community;

use App\Form\Community\Model\TextingCampaignMetaData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextingCampaignMetaDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class)
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
            'data_class' => TextingCampaignMetaData::class,
        ]);
    }
}
