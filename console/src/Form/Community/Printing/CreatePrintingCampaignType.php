<?php

namespace App\Form\Community\Printing;

use App\Form\Community\Printing\Model\CreatePrintingCampaignData;
use App\Platform\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatePrintingCampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('product', ChoiceType::class, [
            'choices' => Products::getPrintProducts(),
            'choice_label' => static fn (string $product) => $product,
            'expanded' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreatePrintingCampaignData::class,
        ]);
    }
}
