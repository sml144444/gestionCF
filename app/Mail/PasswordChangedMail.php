<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $changedAt;
    public string $ipAddress;

    public function __construct(
        public User   $user,
        string        $ipAddress = 'inconnue',
    ) {
        $this->changedAt = now()->translatedFormat('l d F Y à H:i');
        $this->ipAddress = $ipAddress;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Votre mot de passe a été modifié',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
        );
    }
}