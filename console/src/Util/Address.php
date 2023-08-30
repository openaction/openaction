<?php

namespace App\Util;

use Symfony\Component\String\Slugger\AsciiSlugger;

use function Symfony\Component\String\u;

final class Address
{
    private const DETERMINANTS = [
        ' (l\')' => 'l\'',
        ' (la)' => 'la',
        ' (le)' => 'le',
        ' (les)' => 'les',
    ];

    public static function formatCityName(?string $input): ?string
    {
        if (!$input) {
            return null;
        }

        $city = u(trim($input))->lower();

        // Clean determinants
        foreach (self::DETERMINANTS as $suffix => $determinant) {
            if ($city->endsWith($suffix)) {
                $city = $determinant.' '.$city->truncate($city->length() - strlen($suffix));
                break;
            }
        }

        // Sluggify
        $slug = (new AsciiSlugger())->slug(trim($city))->upper();

        // Replace ST by SAINT
        $slug = $slug->replace('-ST-', '-SAINT-');

        if ($slug->startsWith('ST-')) {
            $slug = $slug->replace('ST-', 'SAINT-');
        }

        if ($slug->endsWith('-ST')) {
            $slug = $slug->replace('-ST', '-SAINT');
        }

        // Replace STE by SAINTE
        $slug = $slug->replace('-STE-', '-SAINTE-');

        if ($slug->startsWith('STE-')) {
            $slug = $slug->replace('STE-', 'SAINTE-');
        }

        if ($slug->endsWith('-STE')) {
            $slug = $slug->replace('-STE', '-SAINTE');
        }

        return $slug->toString();
    }
}
