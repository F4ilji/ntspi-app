<?php

namespace App\Providers;

use App\Helpers\ByteConverter;
use Illuminate\Support\ServiceProvider;

class ByteConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('byteconverter', function () {
            return new ByteConverter();
        });
    }

    public function boot()
    {
        //
    }
}
