<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Country field based on the Symfony one.
 * Prioritize display dpending on the likelyhood of customers being there.
 */
class CountryType extends AbstractType
{
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $priorities = [0 => [], 1 => []];

        foreach (Countries::getNames() as $code => $name) {
            if (in_array(strtoupper($code), self::EU_COUNTRIES, true)) {
                $priorities[0][$code] = $code;

                continue;
            }

            $priorities[1][$code] = $code;
        }

        ksort($priorities[0]);
        ksort($priorities[1]);

        $resolver->setDefaults([
            'attr' => ['data-controller' => 'country-select'],
            'choice_translation_domain' => false,
            'choices' => [
                'Europe' => $priorities[0],
                'World' => $priorities[1],
            ],
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'country';
    }
}
