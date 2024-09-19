<?php

namespace App\Services\Filament\Icon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ArrayToCollectionService
{
    public static function all()
    {
        $jsString = file_get_contents(resource_path('js/Components/other/icons.json'));
        $array = json_decode($jsString, true);


        return collect($array);
    }
}