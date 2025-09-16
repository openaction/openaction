<?php

namespace App\Tests\Util;

use App\Util\Spreadsheet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class SpreadsheetTest extends TestCase
{
    public function provideRead()
    {
        yield 'xlsx' => [__DIR__.'/../Fixtures/import/contacts.xlsx'];
    }

    /**
     * @dataProvider provideRead
     */
    public function testRead(string $pathname)
    {
        $expected = [
            [0, 'Email', 'First Name', 'Last Name', 'Gender', 'Country', 'Age', 'Date', 'Id'],
            [1, 'abril.dulce@citipo.com', 'Dulce', 'Abril', 'Female', 'United States', 32, '15/10/2017', 1562],
            [2, 'mara.hashimoto@citipo.com', 'Mara', 'Hashimoto', 'Female', 'Great Britain', 25, '16/08/2016', 1582],
            [3, 'philip.gent@citipo.com', 'Hervé', 'Gent', 'Male', 'France', 36, '21/05/2015', 2587],
        ];

        $this->assertSame($expected, iterator_to_array(Spreadsheet::open(new File($pathname))));
        $this->assertSame([$expected[0]], Spreadsheet::open(new File($pathname))->getFirstLines(1));
    }

    public function providePerformanceRead()
    {
        yield 'xlsx' => [__DIR__.'/../Fixtures/import/contacts-big.xlsx'];
    }

    /**
     * @dataProvider providePerformanceRead
     */
    public function testPerformanceRead(string $pathname)
    {
        // Ensure reading the spreadsheet head can be done in the UI process (takes < 3 sec)
        $startTime = microtime(true);
        Spreadsheet::open(new File($pathname))->getFirstLines(6);
        $this->assertLessThan(3, microtime(true) - $startTime);
    }
}
