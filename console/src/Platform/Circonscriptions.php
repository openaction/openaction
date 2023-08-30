<?php

namespace App\Platform;

final class Circonscriptions
{
    private const FR = [
        '01' => ['Ain', 5],
        '02' => ['Aisne', 5],
        '03' => ['Allier', 3],
        '06' => ['Alpes-Maritimes', 9],
        '04' => ['Alpes-de-Haute-Provence', 2],
        '08' => ['Ardennes', 3],
        '07' => ['Ardèche', 3],
        '09' => ['Ariège', 2],
        '10' => ['Aube', 3],
        '11' => ['Aude', 3],
        '12' => ['Aveyron', 3],
        '67' => ['Bas-Rhin', 9],
        '13' => ['Bouches-du-Rhône', 16],
        '14' => ['Calvados', 6],
        '15' => ['Cantal', 2],
        '16' => ['Charente', 3],
        '17' => ['Charente-Maritime', 5],
        '18' => ['Cher', 3],
        '19' => ['Corrèze', 2],
        '2A' => ['Corse-du-Sud', 2],
        '23' => ['Creuse', 1],
        '21' => ['Côte-d\'Or', 5],
        '22' => ['Côtes-d\'Armor', 5],
        '79' => ['Deux-Sèvres', 3],
        '24' => ['Dordogne', 4],
        '25' => ['Doubs', 5],
        '26' => ['Drôme', 4],
        '91' => ['Essonne', 10],
        '27' => ['Eure', 5],
        '28' => ['Eure-et-Loir', 4],
        '29' => ['Finistère', 8],
        'ZZ' => ['Français établis hors de France', 11],
        '30' => ['Gard', 6],
        '32' => ['Gers', 2],
        '33' => ['Gironde', 12],
        'ZA' => ['Guadeloupe', 4],
        'ZC' => ['Guyane', 2],
        '68' => ['Haut-Rhin', 6],
        '2B' => ['Haute-Corse', 2],
        '31' => ['Haute-Garonne', 10],
        '43' => ['Haute-Loire', 2],
        '52' => ['Haute-Marne', 2],
        '74' => ['Haute-Savoie', 6],
        '70' => ['Haute-Saône', 2],
        '87' => ['Haute-Vienne', 3],
        '05' => ['Hautes-Alpes', 2],
        '65' => ['Hautes-Pyrénées', 2],
        '92' => ['Hauts-de-Seine', 13],
        '34' => ['Hérault', 9],
        '35' => ['Ille-et-Vilaine', 8],
        '36' => ['Indre', 2],
        '37' => ['Indre-et-Loire', 5],
        '38' => ['Isère', 10],
        '39' => ['Jura', 3],
        'ZD' => ['La Réunion', 7],
        '40' => ['Landes', 3],
        '41' => ['Loir-et-Cher', 3],
        '42' => ['Loire', 6],
        '44' => ['Loire-Atlantique', 10],
        '45' => ['Loiret', 6],
        '46' => ['Lot', 2],
        '47' => ['Lot-et-Garonne', 3],
        '48' => ['Lozère', 1],
        '49' => ['Maine-et-Loire', 7],
        '50' => ['Manche', 4],
        '51' => ['Marne', 5],
        'ZB' => ['Martinique', 4],
        '53' => ['Mayenne', 3],
        'ZM' => ['Mayotte', 2],
        '54' => ['Meurthe-et-Moselle', 6],
        '55' => ['Meuse', 2],
        '56' => ['Morbihan', 6],
        '57' => ['Moselle', 9],
        '58' => ['Nièvre', 2],
        '59' => ['Nord', 21],
        'ZN' => ['Nouvelle-Calédonie', 2],
        '60' => ['Oise', 7],
        '61' => ['Orne', 3],
        '75' => ['Paris', 18],
        '62' => ['Pas-de-Calais', 12],
        'ZP' => ['Polynésie française', 3],
        '63' => ['Puy-de-Dôme', 5],
        '64' => ['Pyrénées-Atlantiques', 6],
        '66' => ['Pyrénées-Orientales', 4],
        '69' => ['Rhône', 14],
        'ZX' => ['Saint-Martin/Saint-Barthélemy', 1],
        'ZS' => ['Saint-Pierre-et-Miquelon', 1],
        '72' => ['Sarthe', 5],
        '73' => ['Savoie', 4],
        '71' => ['Saône-et-Loire', 5],
        '76' => ['Seine-Maritime', 10],
        '93' => ['Seine-Saint-Denis', 12],
        '77' => ['Seine-et-Marne', 11],
        '80' => ['Somme', 5],
        '81' => ['Tarn', 3],
        '82' => ['Tarn-et-Garonne', 2],
        '90' => ['Territoire de Belfort', 2],
        '95' => ['Val-d\'Oise', 10],
        '94' => ['Val-de-Marne', 11],
        '83' => ['Var', 8],
        '84' => ['Vaucluse', 5],
        '85' => ['Vendée', 5],
        '86' => ['Vienne', 4],
        '88' => ['Vosges', 4],
        'ZW' => ['Wallis et Futuna', 1],
        '89' => ['Yonne', 3],
        '78' => ['Yvelines', 12],
    ];

    public static function getFrChoices(): array
    {
        $circonscriptions = [];
        foreach (self::FR as $code => [$name, $totalCircos]) {
            for ($i = 1; $i <= $totalCircos; ++$i) {
                $circonscriptions[self::getFrName($code.'-'.$i)] = $code.'-'.$i;
            }
        }

        return $circonscriptions;
    }

    public static function getFrName(string $circoCode): string
    {
        [$code, $i] = explode('-', $circoCode);

        return self::getFrDepartmentName($code).' - '.$i.('1' === $i ? 'ère' : 'ème').' circonscription';
    }

    public static function getFrDepartmentName(string $deptCode): string
    {
        return self::FR[$deptCode][0];
    }
}
