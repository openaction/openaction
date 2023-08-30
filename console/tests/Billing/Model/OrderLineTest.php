<?php

namespace App\Tests\Billing\Model;

use App\Billing\Model\OrderLine;
use App\Tests\UnitTestCase;

class OrderLineTest extends UnitTestCase
{
    public function provideOrderLine()
    {
        yield [
            'line' => [
                'quantity' => 1280,
                'vatRate' => 20.0,
                'unitPrice' => 0.003,
            ],
            'expected' => [
                'quantity' => 1280,
                'vatRate' => 20.0,
                'unitPriceExclTax' => 0.003,
                'unitPriceInclTax' => 0.0036,
                'totalAmountExclTax' => 3.84,
                'totalAmountInclTax' => 4.608,
                'totalVatAmount' => 0.768,
            ],
        ];

        yield [
            'line' => [
                'quantity' => 1280,
                'vatRate' => 20.0,
                'unitPrice' => 0.01234544,
            ],
            'expected' => [
                'quantity' => 1280,
                'vatRate' => 20.0,
                'unitPriceExclTax' => 0.012345,
                'unitPriceInclTax' => 0.014814,
                'totalAmountExclTax' => 15.8016,
                'totalAmountInclTax' => 18.96192,
                'totalVatAmount' => 3.16032,
            ],
        ];

        yield [
            'line' => [
                'quantity' => 50_000,
                'vatRate' => 5.5,
                'unitPrice' => 1.1298,
            ],
            'expected' => [
                'quantity' => 50_000,
                'vatRate' => 5.5,
                'unitPriceExclTax' => 1.1298,
                'unitPriceInclTax' => 1.191939,
                'totalAmountExclTax' => 56_490.0,
                'totalAmountInclTax' => 59_596.95,
                'totalVatAmount' => 3_106.95,
            ],
        ];
    }

    /**
     * @dataProvider provideOrderLine
     */
    public function testOrderLine(array $line, array $expected)
    {
        $l = new OrderLine(OrderLine::TYPE_PRODUCT, 'Name', 'Description', $line['quantity'], $line['unitPrice'], $line['vatRate']);

        $this->assertSame($expected['quantity'], $l->getQuantity());
        $this->assertSame($expected['vatRate'], $l->getVatRate());
        $this->assertSame($expected['unitPriceExclTax'], $l->getUnitPriceExludingTaxes());
        $this->assertSame($expected['unitPriceInclTax'], $l->getUnitPriceIncludingTaxes());
        $this->assertSame($expected['totalAmountExclTax'], $l->getTotalAmountExcludingTaxes());
        $this->assertSame($expected['totalAmountInclTax'], $l->getTotalAmountIncludingTaxes());
        $this->assertSame($expected['totalVatAmount'], $l->getTotalVatAmount());
    }
}
