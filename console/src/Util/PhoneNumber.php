<?php

namespace App\Util;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber as PhoneNumberModel;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

final class PhoneNumber
{
    public static function format(?PhoneNumberModel $number): ?string
    {
        if (!$number) {
            return null;
        }

        return PhoneNumberUtil::getInstance()->format($number, PhoneNumberFormat::INTERNATIONAL);
    }

    public static function formatDatabase(?PhoneNumberModel $number): ?string
    {
        if (!$number) {
            return null;
        }

        return PhoneNumberUtil::getInstance()->format($number, PhoneNumberFormat::E164);
    }

    public static function parse(?string $number, ?string $country): ?PhoneNumberModel
    {
        if (!$number) {
            return null;
        }

        $number = str_replace([' ', '.', '-'], '', $number);

        try {
            $phoneNumber = PhoneNumberUtil::getInstance()->parse($number, strtoupper($country ?: 'FR'));
        } catch (NumberParseException) {
            return null;
        }

        return PhoneNumberUtil::getInstance()->isValidNumber($phoneNumber) ? $phoneNumber : null;
    }
}
