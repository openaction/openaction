<?php

namespace App\Util;

class Chart
{
    public const PRECISION_HOUR = 3600;
    public const PRECISION_DAY = 86400;

    public static function createEmptyDateChart(\DateTime $startDate, int $precision, $defaultValue = 0): array
    {
        $date = clone $startDate;
        $date->modify('+1 day');

        $now = (new \DateTime())->format('Y-m-d');

        $data = [];
        while ($date->format('Y-m-d') <= $now) {
            $data[self::formatDateToPrecision($date, $precision)] = $defaultValue;

            if (self::PRECISION_HOUR === $precision) {
                $date->modify('+1 hour');
            } else {
                $date->modify('+1 day');
            }
        }

        return $data;
    }

    public static function formatDateToPrecision(\DateTime $date, int $precision)
    {
        if (self::PRECISION_HOUR === $precision) {
            return $date->format('Y-m-d H:i');
        }

        return $date->format('Y-m-d');
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
