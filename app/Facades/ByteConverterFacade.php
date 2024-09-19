<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ByteConverterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'byteconverter';
    }
}