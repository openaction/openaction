<?php

namespace App\Form\Community\Printing;

use App\Form\Community\Printing\Model\PrintingOrderRecipientData;
use App\Platform\Circonscriptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintingOrderRecipientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('circonscription', ChoiceType::class, ['required' => true, 'choices' => Circonscriptions::getFrChoices()])
            ->add('candidate', TextType::class, ['required' => true])
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('phone', TextType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrintingOrderRecipientData::class,
        ]);
    }
}
