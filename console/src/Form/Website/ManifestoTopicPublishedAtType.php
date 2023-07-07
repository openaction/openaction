<?php

namespace App\Form\Website;

use App\Form\Website\Model\ManifestoTopicPublishedAtData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManifestoTopicPublishedAtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('publishedAt', HiddenType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ManifestoTopicPublishedAtData::class,
            'csrf_protection' => false,
        ]);
    }
}
