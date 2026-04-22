<?php

namespace App\Mail;

use App\Models\Edu;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEduMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Edu    $edu,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Vos identifiants OFPPT – Activez votre compte',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-edu',
        );
    }
}