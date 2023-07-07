<?php

namespace App\Form\Community\Printing;

use App\Entity\Community\PrintingCampaign;
use App\Platform\Prices;
use App\Platform\PrintFiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrintingOrderUnaddressedQuantitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var PrintingCampaign[] $campaigns */
        $campaigns = $options['campaigns'];

        if ($options['is_subrogated']) {
            // Subrogated: allow custom quantities
            foreach ($campaigns as $campaign) {
                $builder->add($campaign->getId(), NumberType::class, ['required' => true]);
            }
        } else {
            // Not subrogated: allow only standard quantities
            foreach ($campaigns as $campaign) {
                $builder->add($campaign->getId(), ChoiceType::class, [
                    'required' => true,
                    'choices' => PrintFiles::QUANTITIES_BY_PRODUCT[$campaign->getProduct()],
                    'choice_label' => static function (string $value) use ($campaign) {
                        return number_format($value, 0, ',', ' ').
                            ' ('.Prices::PRINT_PRODUCTION[$campaign->getProduct()][$value].' € HT)';
                    },
                    'constraints' => [
                        new NotBlank(),
                        new Choice(['choices' => PrintFiles::QUANTITIES_BY_PRODUCT[$campaign->getProduct()]]),
                    ],
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('is_subrogated');
        $resolver->setAllowedTypes('is_subrogated', 'bool');

        $resolver->setRequired('campaigns');
        $resolver->setAllowedTypes('campaigns', 'iterable');
    }
}
