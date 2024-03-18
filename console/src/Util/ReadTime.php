<?php

namespace App\Util;

class ReadTime
{
    public static function inMinutes(?string $content): int
    {
        return $content ? ceil(count(explode(' ', str_replace("\n", ' ', strip_tags($content)))) / 200) : 0;
    }
}
