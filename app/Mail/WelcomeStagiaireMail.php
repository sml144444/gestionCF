<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeStagiaireMail extends Mailable
{
    use Queueable, SerializesModels;

    // No plainPassword here — stagiaire already knows their password,
    // they just used it to activate their account.
    public function __construct(public User $user ,public string $plainPassword) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Votre compte OFPPT est activé',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-stagiaire',
        );
    }
}