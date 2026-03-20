<?php

namespace App\Ship\Abstracts\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable as LaravelMailable;
use Illuminate\Queue\SerializesModels;

abstract class Mailable extends LaravelMailable
{
    use Queueable, SerializesModels;
}
