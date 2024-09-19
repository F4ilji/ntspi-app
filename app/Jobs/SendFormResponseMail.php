<?php

namespace App\Jobs;

use App\Mail\CustomFormResponseMail;
use App\Models\CustomFormResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFormResponseMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CustomFormResponse $customFormResponse;

    /**
     * Create a new job instance.
     */
    public function __construct(CustomFormResponse $customFormResponse)
    {
        $this->customFormResponse = $customFormResponse;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mail_settings = $this->customFormResponse->form->mail_settings;
        foreach ($mail_settings as $setting) {
            Mail::to($setting['target'])
                ->send(new CustomFormResponseMail($setting, $this->customFormResponse->answers, $this->customFormResponse->form->columns));
        }
    }
}
