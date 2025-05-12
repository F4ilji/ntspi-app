<?php

namespace App\Containers\Widget\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomFormResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $settings;
    public array $answers;
    public array $columns;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $settings, array $answers, array $columns)
    {
        $this->settings = $settings;
        $this->answers = $answers;
        $this->columns = $columns;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->settings['topic'], // Используем тему из настроек
        );
    }


    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-form-response', // Укажите путь к вашему шаблону
            with: [
                'data' => $this->settings['data'],
                'answers' => $this->answers,
                'columns' => $this->columns,

            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}