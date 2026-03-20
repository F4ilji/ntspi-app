<?php

use App\Containers\User\UI\WEB\Controllers\PersonController;
use Illuminate\Support\Facades\Route;



Route::middleware('signed')->get('invitation/{invitation}/accept', \App\Livewire\AcceptInvitation::class)
    ->name('invitation.accept');


Route::middleware('access-check')->group(function () {
    Route::get('/persons/{slug}', [PersonController::class, 'show'])->name('client.person.show');
});

