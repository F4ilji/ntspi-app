<?php

namespace App\Observers;

use App\Jobs\SendFormResponseMail;
use App\Mail\CustomFormResponseMail;
use App\Models\CustomFormResponse;
use Illuminate\Support\Facades\Mail;

class CustomFormResponseObserver
{
    /**
     * Handle the CustomFormResponse "created" event.
     */
    public function created(CustomFormResponse $customFormResponse): void
    {
        dispatch(new SendFormResponseMail($customFormResponse));
    }

    /**
     * Handle the CustomFormResponse "updated" event.
     */
    public function updated(CustomFormResponse $customFormResponse): void
    {
        //
    }

    /**
     * Handle the CustomFormResponse "deleted" event.
     */
    public function deleted(CustomFormResponse $customFormResponse): void
    {
        //
    }

    /**
     * Handle the CustomFormResponse "restored" event.
     */
    public function restored(CustomFormResponse $customFormResponse): void
    {
        //
    }

    /**
     * Handle the CustomFormResponse "force deleted" event.
     */
    public function forceDeleted(CustomFormResponse $customFormResponse): void
    {
        //
    }
}
