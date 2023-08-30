<?php

namespace App\Form\Community;

use App\Form\Community\Model\EmailingCampaignMetaData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailingCampaignMetaDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class)
            ->add('preview', TextType::class, ['required' => false])
            ->add('fromEmail', TextType::class)
            ->add('fromName', TextType::class, ['required' => false])
            ->add('replyToEmail', TextType::class, ['required' => false])
            ->add('replyToName', TextType::class, ['required' => false])
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
            'data_class' => EmailingCampaignMetaData::class,
        ]);
    }
}
