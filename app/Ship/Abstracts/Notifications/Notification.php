<?php

namespace App\Ship\Abstracts\Notifications;

use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Bus\Queueable;

abstract class Notification extends LaravelNotification
{
    use Queueable;
}
