<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customMessage;
    public $status;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $customMessage, $status)
    {
        $this->name = $name;
        $this->customMessage = $customMessage;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تحديث حالة الطلب',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.statusMail',
            with: [
                'customMessage' => $this->customMessage,
                'status' => $this->status,
                'name' => $this->name,

            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
