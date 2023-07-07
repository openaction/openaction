<?php

namespace App\Form\Appearance;

use App\Form\Appearance\Model\WebsiteAccessData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('websiteAccessUser', TextType::class, ['required' => false])
            ->add('websiteAccessPass', TextType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WebsiteAccessData::class,
        ]);
    }
}
