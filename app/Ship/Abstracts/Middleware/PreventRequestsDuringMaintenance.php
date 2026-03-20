<?php

namespace App\Ship\Abstracts\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as LaravelMiddleware;

abstract class PreventRequestsDuringMaintenance extends LaravelMiddleware
{

}
