<?php

namespace App\Mail;

use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolAdminCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly School $school,
        public readonly string $plainPassword,
        public readonly string $plainPin,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your School Admin Account — ' . $this->school->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.superadmin.school-admin-credentials',
        );
    }
}
