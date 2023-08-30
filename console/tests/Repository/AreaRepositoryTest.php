<?php

namespace App\Tests\Repository;

use App\Entity\Area;
use App\Repository\AreaRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AreaRepositoryTest extends KernelTestCase
{
    public function provideSearchCountry()
    {
        yield 'fr_code_uppercase' => ['term' => 'FR', 'expected' => 36778547219895752];
        yield 'fr_code_lowercase' => ['term' => 'fr', 'expected' => 36778547219895752];
        yield 'fr_name_uppercase' => ['term' => 'FRANCE', 'expected' => 36778547219895752];
        yield 'fr_name_lowercase' => ['term' => 'france', 'expected' => 36778547219895752];
        yield 'fr_start_with_uppercase' => ['term' => 'FRA', 'expected' => 36778547219895752];
        yield 'fr_start_with_lowercase' => ['term' => 'fra', 'expected' => 36778547219895752];

        yield 'at_code_uppercase' => ['term' => 'AT', 'expected' => 35199436697483610];
        yield 'at_code_lowercase' => ['term' => 'at', 'expected' => 35199436697483610];
        yield 'at_name_uppercase' => ['term' => 'AUSTRIA', 'expected' => 35199436697483610];
        yield 'at_name_lowercase' => ['term' => 'austria', 'expected' => 35199436697483610];
        yield 'at_start_with_uppercase' => ['term' => 'AUS', 'expected' => 35199436697483610];
        yield 'at_start_with_lowercase' => ['term' => 'aus', 'expected' => 35199436697483610];
    }

    /**
     * @dataProvider provideSearchCountry
     */
    public function testSearchCountry(string $term, int $expectedAreaId)
    {
        self::bootKernel();

        /** @var Area $area */
        $area = static::getContainer()->get(AreaRepository::class)->searchCountry($term);

        $this->assertInstanceOf(Area::class, $area);
        $this->assertSame($expectedAreaId, $area->getId());
    }
}
