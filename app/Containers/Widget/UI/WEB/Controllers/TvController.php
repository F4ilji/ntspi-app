<?php

namespace App\Containers\Widget\UI\WEB\Controllers;

use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;

class TvController extends Controller
{
    public function index()
    {
        return inertia()->render('Client/Tv/Index');
    }
}
