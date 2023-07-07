<?php

namespace App\Form\Partner;

use App\Form\Partner\Model\PartnerMenuData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        for ($i = 1; $i <= 2; ++$i) {
            $builder
                ->add('label'.$i, TextType::class, ['required' => false])
                ->add('url'.$i, UrlType::class, ['required' => false])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PartnerMenuData::class,
        ]);
    }
}
