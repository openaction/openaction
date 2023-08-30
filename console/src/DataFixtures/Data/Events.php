<?php

namespace App\DataFixtures\Data;

final class Events
{
    /**
     * Create events data by moving the raw data to recent dates.
     */
    public static function createDataEnding(string $date): iterable
    {
        // Compute the diff between raw data end and yesterday to move the data accordingly
        $diff = (new \DateTime($date))->diff(new \DateTime('2021-01-02'))->days;

        foreach (self::RAW_DATA as $row) {
            yield [
                'hash' => $row[0],
                'name' => $row[1],
                'date' => (new \DateTime($row[2]))->modify('+'.$diff.' days')->format('Y-m-d H:i:s'),
            ];
        }
    }

    private const RAW_DATA = [
        ['829c0e9e-1171-b7b8-2bcd-9c4d2484ceec', 'level_1', '2021-01-01 20:25:32'],
        ['829c0e9e-1171-b7b8-2bcd-9c4d2484ceec', 'level_2', '2021-01-01 20:28:32'],
        ['829c0e9e-1171-b7b8-2bcd-9c4d2484ceec', 'level_3', '2021-01-01 21:01:32'],
    ];
}
