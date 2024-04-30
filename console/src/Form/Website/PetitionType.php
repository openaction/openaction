<?php

namespace App\Form\Website;

use App\Entity\Website\Petition;
use App\Entity\Website\TrombinoscopePerson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Positive;

class PetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateType::class, ['required' => false, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
            ->add('endAt', DateType::class, ['required' => false, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
            ->add('signaturesGoal', IntegerType::class, [
                'constraints' => [
                    new Positive(),     // Ensure the value is positive
                    new GreaterThan(0),  // Ensure the value is greater than 0
                ],
                'required' => true,
            ])
            ->add('authors', EntityType::class, [
                'class' => TrombinoscopePerson::class,
                'choices' => $options['authors'],
                'choice_label' => 'fullName',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Petition::class,
            'csrf_protection' => false,
            'authors' => null,
        ]);
    }
}
