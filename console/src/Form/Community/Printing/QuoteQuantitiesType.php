<?php

namespace App\Form\Community\Printing;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteQuantitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['products'] as $product) {
            $builder->add($product, NumberType::class, [
                'required' => true,
                'label' => $product,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('products');
        $resolver->setAllowedTypes('products', 'array');
    }
}
