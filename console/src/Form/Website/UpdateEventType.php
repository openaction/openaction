<?php

namespace App\Form\Website;

use App\Form\Website\Model\EventData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('beginAt', DateTimeType::class, [
                'minutes' => [0, 15, 30, 45],
                'required' => false,
            ])
            ->add('timezone', TimezoneType::class, ['required' => false])
            ->add('content', TextareaType::class, ['required' => false, 'attr' => ['rows' => 5]])
            ->add('url', TextType::class, ['required' => false])
            ->add('buttonText', TextType::class, ['required' => false])
            ->add('address', TextType::class, ['required' => false])
            ->add('latitude', HiddenType::class, ['required' => false])
            ->add('longitude', HiddenType::class, ['required' => false])
            ->add('publishedAt', HiddenType::class, ['required' => false])
            ->add('categories', HiddenType::class, ['required' => false])
            ->add('externalUrl', HiddenType::class, ['required' => false])
            ->add('onlyForMembers', HiddenType::class, ['required' => false])
            ->add('participants', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventData::class,
            'validation_groups' => ['Default'],
            'csrf_protection' => false,
        ]);
    }
}
