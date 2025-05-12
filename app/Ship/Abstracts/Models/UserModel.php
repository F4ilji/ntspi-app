<?php

namespace App\Ship\Abstracts\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as LaravelAuthenticate;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

abstract class UserModel extends LaravelAuthenticate
{
    use HasApiTokens, HasFactory, Notifiable;
}
