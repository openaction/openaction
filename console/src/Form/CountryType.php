<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Country field based on the Symfony one.
 * Prioritize display depending on the likelyhood of customers being there.
 */
class CountryType extends AbstractType
{
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    // Countries to exclude from the list due to international sanctions
    private const BLOCKED_COUNTRIES = [
        // Sanctionned
        'CU', // Cuba
        'IR', // Iran
        'LY', // Libya
        'KP', // North Korea
        'NI', // Nicaragua
        'SO', // Somalia
        'SD', // Sudan
        'SY', // Syria
        'VE', // Venezuela
        'YE', // Yemen

        // Restricted
        'BY', // Belarus
        'CD', // Congo - Kinshasa
        'CI', // Cote d’Ivoire
        'IQ', // Iraq
        'LR', // Liberia
        'MM', // Myanmar
        'SL', // Sierra Leone
        'ZW', // Zimbabwe
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $priorities = [0 => [], 1 => []];

        foreach (Countries::getNames() as $code => $name) {
            if (in_array($code, self::BLOCKED_COUNTRIES, true)) {
                continue;
            }

            if (in_array(strtoupper($code), self::EU_COUNTRIES, true)) {
                $priorities[0][$name] = $code;

                continue;
            }

            $priorities[1][$name] = $code;
        }

        ksort($priorities);

        $resolver->setDefaults([
            'choice_translation_domain' => false,
            'choices' => [
                'Europe' => $priorities[0],
                'World' => $priorities[1],
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'country';
    }
}
