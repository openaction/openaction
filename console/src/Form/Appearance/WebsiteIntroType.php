<?php

namespace App\Form\Appearance;

use App\Form\Appearance\Model\WebsiteIntroData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteIntroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('websiteMainImage', FileType::class, ['required' => false])
            ->add('websiteMainVideo', FileType::class, ['required' => false])
            ->add('websiteMainIntroTitle', TextType::class, ['required' => false])
            ->add('websiteMainIntroContent', TextareaType::class, ['required' => false, 'attr' => ['rows' => 2]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WebsiteIntroData::class,
        ]);
    }
}
