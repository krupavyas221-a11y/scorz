<?php

namespace App\Mail;

use App\Models\Pupil;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PupilCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Pupil  $pupil,
        public readonly string $plainPin,
        public readonly User   $recipient, // school admin or teacher
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Pupil Account — ' . $this->pupil->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.superadmin.pupil-created',
        );
    }
}
