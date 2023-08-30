<?php

namespace App\Util;

use Symfony\Component\Uid\Uuid;

class Uid
{
    public static function random(): Uuid
    {
        return Uuid::v4();
    }

    public static function fixed(string $name): Uuid
    {
        return Uuid::v5(Uuid::fromString('6ba7b812-9dad-11d1-80b4-00c04fd430c8'), $name);
    }

    public static function toBase62(Uuid $uuid): string
    {
        return gmp_strval(gmp_init(str_replace('-', '', $uuid->toRfc4122()), 16), 62);
    }

    public static function fromBase62(string $encoded): ?Uuid
    {
        try {
            return Uuid::fromString(array_reduce(
                [20, 16, 12, 8],
                static function ($uuid, $offset) {
                    return substr_replace($uuid, '-', $offset, 0);
                },
                str_pad(gmp_strval(gmp_init($encoded, 62), 16), 32, '0', STR_PAD_LEFT)
            ));
        } catch (\Throwable) {
            return null;
        }
    }
}
