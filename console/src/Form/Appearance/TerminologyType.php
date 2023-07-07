<?php

namespace App\Form\Appearance;

use App\Entity\Model\ProjectTerminology;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerminologyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('posts', TextType::class, ['required' => true])
            ->add('events', TextType::class, ['required' => true])
            ->add('trombinoscope', TextType::class, ['required' => true])
            ->add('manifesto', TextType::class, ['required' => true])
            ->add('newsletter', TextType::class, ['required' => true])
            ->add('acceptPrivacy', TextType::class, ['required' => true])
            ->add('socialNetworks', TextType::class, ['required' => true])
            ->add('membershipLogin', TextType::class, ['required' => true])
            ->add('membershipRegister', TextType::class, ['required' => true])
            ->add('membershipArea', TextType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectTerminology::class,
        ]);
    }
}
