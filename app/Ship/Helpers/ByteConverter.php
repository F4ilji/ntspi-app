<?php

namespace App\Ship\Helpers;

class ByteConverter
{
    public static function bytesToHuman($bytes)
    {
        $units = ['Б', 'КиБ', 'МиБ', 'ГиБ', 'ТиБ', 'ПиБ'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}