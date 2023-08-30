<?php

namespace App\Tests\Util;

use App\Util\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function provideFormatCityName()
    {
        yield 'null' => [null, null];
        yield 'simple' => ['Paris', 'PARIS'];
        yield 'complex' => ['Saint-Hilaire du Harcouët', 'SAINT-HILAIRE-DU-HARCOUET'];
        yield 'trim' => ["\nParis ", 'PARIS'];
        yield 'saint-start' => ['St Hilaire', 'SAINT-HILAIRE'];
        yield 'saint-inside' => ['Bussy-St-Georges', 'BUSSY-SAINT-GEORGES'];
        yield 'saint-end' => ['Bussy St', 'BUSSY-SAINT'];
        yield 'saint-without-start' => ['Strasbourg', 'STRASBOURG'];
        yield 'saint-without-inside' => ['Savigny Lès Strasbourg', 'SAVIGNY-LES-STRASBOURG'];
        yield 'saint-without-end' => ['Est', 'EST'];
        yield 'sainte-start' => ['Ste Hilaire', 'SAINTE-HILAIRE'];
        yield 'sainte-inside' => ['Bussy-Ste-Georges', 'BUSSY-SAINTE-GEORGES'];
        yield 'sainte-end' => ['Bussy Ste', 'BUSSY-SAINTE'];
        yield 'sainte-without-start' => ['Sterasbourg', 'STERASBOURG'];
        yield 'sainte-without-inside' => ['Savigny Lès Sterasbourg', 'SAVIGNY-LES-STERASBOURG'];
        yield 'sainte-without-end' => ['Este', 'ESTE'];
        yield 'determinant' => ['TORQUESNE (LE)', 'LE-TORQUESNE'];
    }

    /**
     * @dataProvider provideFormatCityName
     */
    public function testFormatCityName(?string $city, ?string $expected)
    {
        $this->assertSame($expected, Address::formatCityName($city));
    }
}
