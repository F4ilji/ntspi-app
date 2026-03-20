<?php

namespace App\Observers;

use App\Containers\Widget\Models\CustomFormResponse;
use App\Jobs\SendFormResponseMail;

class CustomFormResponseObserver
{
    /**
     * Handle the CustomFormResponse "created" event.
     */
    public function created(CustomFormResponse $customFormResponse): void
    {
        !empty($customFormResponse->form->mail_settings) ? dispatch(new SendFormResponseMail($customFormResponse)) : null;
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
