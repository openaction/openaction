<?php

namespace App\Platform;

final class PrintFiles
{
    public const PAGES_BY_PRODUCT = [
        Products::PRINT_OFFICIAL_POSTER => 1,
        Products::PRINT_OFFICIAL_BANNER => 1,
        Products::PRINT_OFFICIAL_BALLOT => 1,
        Products::PRINT_OFFICIAL_PLEDGE => 2,
        Products::PRINT_CAMPAIGN_FLYER => 2,
        Products::PRINT_CAMPAIGN_LARGE_FLYER => 2,
        Products::PRINT_CAMPAIGN_DOOR => 2,
        Products::PRINT_CAMPAIGN_BOOKLET_4 => 4,
        Products::PRINT_CAMPAIGN_BOOKLET_8 => 8,
        Products::PRINT_CAMPAIGN_LETTER => 1,
        Products::PRINT_CAMPAIGN_POSTER => 1,
        Products::PRINT_CAMPAIGN_CARD => 2,
    ];

    public const SIZE_BY_PRODUCT = [
        // Product => Width (mm), Height (mm), Resolution
        Products::PRINT_OFFICIAL_POSTER => [604, 851, 300],
        Products::PRINT_OFFICIAL_BANNER => [430, 307, 300],
        Products::PRINT_OFFICIAL_PLEDGE => [220, 307, 300],
        Products::PRINT_OFFICIAL_BALLOT => [158, 115, 300],
        Products::PRINT_CAMPAIGN_POSTER => [604, 851, 300],
        Products::PRINT_CAMPAIGN_FLYER => [158, 220, 300],
        Products::PRINT_CAMPAIGN_LARGE_FLYER => [220, 307, 300],
        Products::PRINT_CAMPAIGN_BOOKLET_4 => [158, 220, 300],
        Products::PRINT_CAMPAIGN_BOOKLET_8 => [158, 220, 300],
        Products::PRINT_CAMPAIGN_LETTER => [220, 307, 300],
        Products::PRINT_CAMPAIGN_DOOR => [70, 220, 300],
        Products::PRINT_CAMPAIGN_CARD => [158, 115, 300],
    ];

    public const QUANTITIES_BY_PRODUCT = [
        Products::PRINT_OFFICIAL_POSTER => [150, 300, 450],
        Products::PRINT_OFFICIAL_BANNER => [150, 300, 450],
        Products::PRINT_OFFICIAL_BALLOT => [100_000, 150_000, 200_000, 250_000, 300_000],
        Products::PRINT_OFFICIAL_PLEDGE => [50_000, 75_000, 100_000, 125_000, 150_000],
        Products::PRINT_CAMPAIGN_FLYER => [5_000, 10_000, 25_000, 50_000],
        Products::PRINT_CAMPAIGN_LARGE_FLYER => [50_000, 75_000, 100_000, 125_000],
        Products::PRINT_CAMPAIGN_DOOR => [1_000, 3_000, 5_000],
        Products::PRINT_CAMPAIGN_BOOKLET_4 => [5_000, 10_000, 25_000, 50_000],
        Products::PRINT_CAMPAIGN_BOOKLET_8 => [5_000, 10_000, 25_000, 50_000],
        Products::PRINT_CAMPAIGN_LETTER => [50_000, 75_000, 100_000, 125_000],
        Products::PRINT_CAMPAIGN_POSTER => [150, 300, 450],
        Products::PRINT_CAMPAIGN_CARD => [5_000, 10_000, 25_000, 50_000],
    ];

    public const WEIGHT_BY_PRODUCT = [
        Products::PRINT_OFFICIAL_POSTER => [150 => 0, 300 => 0, 450 => 0], // Included in production price
        Products::PRINT_OFFICIAL_BANNER => [150 => 0, 300 => 0, 450 => 0], // Included in production price
        Products::PRINT_OFFICIAL_PLEDGE => [50_000 => 219, 75_000 => 328, 100_000 => 437, 125_000 => 546, 150_000 => 655],
        Products::PRINT_OFFICIAL_BALLOT => [100_000 => 109, 150_000 => 164, 200_000 => 218, 250_000 => 272, 300_000 => 327],
        Products::PRINT_CAMPAIGN_FLYER => [5_000 => 11, 10_000 => 22, 25_000 => 55, 50_000 => 110],
        Products::PRINT_CAMPAIGN_LARGE_FLYER => [50_000 => 219, 75_000 => 328, 100_000 => 437, 125_000 => 546],
        Products::PRINT_CAMPAIGN_DOOR => [1_000 => 3, 3_000 => 8, 5_000 => 13],
        Products::PRINT_CAMPAIGN_BOOKLET_4 => [5_000 => 22, 10_000 => 44, 25_000 => 110, 50_000 => 220],
        Products::PRINT_CAMPAIGN_BOOKLET_8 => [5_000 => 44, 10_000 => 88, 25_000 => 220, 50_000 => 440],
        Products::PRINT_CAMPAIGN_LETTER => [50_000 => 219, 75_000 => 328, 100_000 => 437, 125_000 => 546],
        Products::PRINT_CAMPAIGN_POSTER => [150 => 0, 300 => 0, 450 => 0], // Included in production price
        Products::PRINT_CAMPAIGN_CARD => [5_000 => 16, 10_000 => 32, 25_000 => 78, 50_000 => 156],
    ];

    public const PRODCODE_BY_PRODUCT = [
        Products::PRINT_OFFICIAL_POSTER => 'AO',
        Products::PRINT_OFFICIAL_BANNER => 'BA',
        Products::PRINT_OFFICIAL_PLEDGE => 'PF',
        Products::PRINT_OFFICIAL_BALLOT => 'BV',
        Products::PRINT_CAMPAIGN_FLYER => 'TR',
        Products::PRINT_CAMPAIGN_LARGE_FLYER => 'A4',
        Products::PRINT_CAMPAIGN_DOOR => 'AP',
        Products::PRINT_CAMPAIGN_BOOKLET_4 => '4P',
        Products::PRINT_CAMPAIGN_BOOKLET_8 => '8P',
        Products::PRINT_CAMPAIGN_LETTER => 'A4',
        Products::PRINT_CAMPAIGN_POSTER => 'A1',
        Products::PRINT_CAMPAIGN_CARD => 'CP',
    ];

    public static function convertPixelsToMm(string $product, int $pixels): int
    {
        if (!$size = self::SIZE_BY_PRODUCT[$product] ?? null) {
            throw new \InvalidArgumentException('Invalid product '.$product);
        }

        return (int) (round(($pixels * 25.4) / $size[2], 1) * 10);
    }
}
