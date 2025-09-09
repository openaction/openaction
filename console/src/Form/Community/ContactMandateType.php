<?php

namespace App\Form\Community;

use App\Entity\Community\Enum\ContactMandateType as MandateEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactMandateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => MandateEnum::cases(),
                'choice_label' => fn (MandateEnum $c) => $c->name,
                'choice_value' => fn (?MandateEnum $c) => $c?->value,
                'required' => true,
            ])
            ->add('label', TextType::class, ['required' => true])
            ->add('startAt', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endAt', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
