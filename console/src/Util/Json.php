<?php

namespace App\Util;

class Json
{
    public static function encode($data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public static function decode(?string $data)
    {
        if (!$data) {
            return null;
        }

        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }
}
