<?php

namespace App\Form\Website;

use App\Form\Website\Model\ManifestoProposalData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManifestoProposalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', HiddenType::class)
            ->add('content', HiddenType::class)
            ->add('status', HiddenType::class)
            ->add('statusDescription', HiddenType::class)
            ->add('statusCtaText', HiddenType::class)
            ->add('statusCtaUrl', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ManifestoProposalData::class,
            'csrf_protection' => false,
        ]);
    }
}
