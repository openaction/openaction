<?php

namespace App\Form\Appearance;

use App\Form\Appearance\Model\LogosData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('appearanceLogoDark', FileType::class, ['required' => false])
            ->add('appearanceLogoWhite', FileType::class, ['required' => false])
            ->add('appearanceIcon', FileType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LogosData::class,
        ]);
    }
}
