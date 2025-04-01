<?php

namespace App\Util;

class Chart
{
    public static function createEmptyDateChart(\DateTime $startDate, $defaultValue = 0): array
    {
        $date = clone $startDate;
        $date->modify('+1 day');

        $now = (new \DateTime())->format('Y-m-d');

        $data = [];
        while ($date->format('Y-m-d') <= $now) {
            $data[$date->format('Y-m-d')] = $defaultValue;
            $date->modify('+1 day');
        }

        return $data;
    }

    public static function formatAsPercentages(array $data): array
    {
        $total = 0;
        $values = [];

        foreach ($data as $key => $count) {
            $total += $count;
            $values[$key] = $count;
        }

        foreach ($values as $key => $count) {
            $values[$key] = round($count / $total, 3) * 100;
        }

        return $values;
    }
}
