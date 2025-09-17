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
        $hex = str_replace('-', '', $uuid->toRfc4122());

        if (\function_exists('gmp_init')) {
            return gmp_strval(gmp_init($hex, 16), 62);
        }

        // Fallback without GMP: convert hex (base16) to base62 using arbitrary-precision division
        return self::convertBase($hex, 16, 62);
    }

    public static function fromBase62(string $encoded): ?Uuid
    {
        try {
            $hex = null;

            if (\function_exists('gmp_init')) {
                $hex = str_pad(gmp_strval(gmp_init($encoded, 62), 16), 32, '0', STR_PAD_LEFT);
            } else {
                // Fallback without GMP: convert base62 to hex (base16)
                $hex = str_pad(self::convertBase($encoded, 62, 16), 32, '0', STR_PAD_LEFT);
            }

            return Uuid::fromString(array_reduce(
                [20, 16, 12, 8],
                static function ($uuid, $offset) {
                    return substr_replace($uuid, '-', $offset, 0);
                },
                $hex
            ));
        } catch (\Throwable) {
            return null;
        }
    }

    private static function convertBase(string $number, int $fromBase, int $toBase): string
    {
        $digits = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $number = strtolower($number);

        // Convert input string to array of integer digits in fromBase
        $n = [];
        $len = strlen($number);
        for ($i = 0; $i < $len; ++$i) {
            $ch = $number[$i];
            $pos = strpos($digits, $ch);
            if (false === $pos || $pos >= $fromBase) {
                throw new \InvalidArgumentException('Invalid number for base conversion');
            }
            $n[] = $pos;
        }

        if (empty($n)) {
            return '0';
        }

        $result = '';
        while (!empty($n)) {
            $quotient = [];
            $remainder = 0;
            foreach ($n as $digit) {
                $acc = $remainder * $fromBase + $digit;
                $q = intdiv($acc, $toBase);
                $remainder = $acc % $toBase;
                if (!empty($quotient) || 0 !== $q) {
                    $quotient[] = $q;
                }
            }
            $result = $digits[$remainder].$result;
            $n = $quotient;
        }

        // For hex output, ensure lowercase characters
        if (16 === $toBase) {
            $result = strtolower($result);
        }

        return '' === $result ? '0' : $result;
    }
}
