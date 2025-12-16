<?php

namespace App\Http\Controllers;

/**
 * @deprecated Use App\Containers\Main\UI\WEB\Controllers\MainController instead
 * This controller is deprecated and will be removed in future versions.
 * All logic has been moved to the Main container following Porto architecture.
 */
class MainController extends Controller
{
    public function index()
    {
        // This method is deprecated. Use App\Containers\Main\UI\WEB\Controllers\MainController instead.
        abort(500, 'MainController in Http namespace is deprecated. Use the containerized version.');
    }
}
