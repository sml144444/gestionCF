<?php

// app/Mail/WeeklyScheduleNotification.php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyScheduleNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly mixed      $stagiaire,
        public readonly Carbon     $weekStart,
        public readonly int        $sessionCount,
        public readonly Collection $subjects = new Collection(),
    ) {}

    public function envelope(): Envelope
    {
        $date = $this->weekStart->translatedFormat('d M Y');

        return new Envelope(
            subject: "📅 Votre emploi du temps — semaine du {$date} est disponible",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly_schedule',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}