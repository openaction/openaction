<?php

namespace App\Platform;

final class Companies
{
    public const CITIPO = 'citipo';
    public const CID = 'cid';

    public const BILLING = [
        self::CITIPO => [
            'code' => self::CITIPO,
            'name' => 'Citipo',
            'addressName' => 'CPGT SAS',
            'addressStreet' => '49 Rue de Ponthieu',
            'addressCity' => '75008 Paris FRANCE',
            'email' => 'billing@citipo.com',
            'vatNumber' => 'FR71852791169',
            'structure' => 'SAS au capital de 5000 €',
            'siret' => '85279116900018',
            'rcs' => 'Paris 852 791 169',
            'bankName' => 'Qonto (Olinda SAS)',
            'iban' => 'FR76 1695 8000 0185 8865 1211 752',
            'bic' => 'QNTOFRP1XXX',
        ],
        self::CID => [
            'code' => self::CID,
            'name' => 'CID',
            'addressName' => 'CONSEIL IMPRESSION DISPATCH',
            'addressStreet' => '70-82 Rue Auber',
            'addressCity' => '94400 Vitry-sur-Seine FRANCE',
            'email' => 'legislatives@prenant.fr',
            'vatNumber' => 'FR03808588214',
            'structure' => 'SAS au capital de 100 000 €',
            'siret' => '80858821400011',
            'rcs' => '808 588 214 R.C.S. Créteil',
            'bankName' => 'Crédit du Nord',
            'iban' => 'FR76 3007 6021 4713 4323 0020 052',
            'bic' => 'NORDFRPP',
        ],
    ];
}
