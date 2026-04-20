<?php

namespace App\Mail;

use App\Models\Cours;
use App\Models\EmploiDuTemps;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouveauDocumentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User          $recipient,
        public Cours         $document,
        public EmploiDuTemps $emploi,
        public User          $sharedBy,
        public iterable      $otherDocs = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📄 Nouveau document – ' . ($this->document->titre ?? 'Classroom'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.document',
        );
    }
}