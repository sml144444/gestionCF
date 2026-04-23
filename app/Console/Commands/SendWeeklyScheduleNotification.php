<?php

// app/Console/Commands/SendWeeklyScheduleNotification.php

namespace App\Console\Commands;

use App\Mail\WeeklyScheduleNotification;
use App\Models\EmploiDuTemps;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyScheduleNotification extends Command
{
    protected $signature   = 'schedule:notify-stagiaires
                              {--dry-run : Log only, do not send emails}';
    protected $description = 'Send weekly schedule notification to stagiaires (runs every Sunday at 00:00)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $nextMonday  = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeek();
        $nextSunday  = $nextMonday->copy()->addDays(5)->endOfDay();   // Saturday EOD
        $weekLabel   = $nextMonday->translatedFormat('d M Y');

        $this->info("📅 Checking sessions for week of {$weekLabel}…");

        // ── All groups that have at least one published session next week ──
        $groupeIdsWithSessions = EmploiDuTemps::whereBetween('date_debut', [$nextMonday, $nextSunday])
            ->where('statut', 'actif')
            ->pluck('id_groupe')
            ->unique();

        if ($groupeIdsWithSessions->isEmpty()) {
            $this->warn('⛔ No published sessions next week — no emails sent (vacation week).');
            return self::SUCCESS;
        }

        $this->info("✅ Found sessions in " . $groupeIdsWithSessions->count() . " group(s).");

        // ── Stagiaires whose group has sessions ──
        $stagiaires = User::where('role', 'stagiaire')
            ->whereNotNull('id_groupe')
            ->whereNotNull('email')
            ->whereIn('id_groupe', $groupeIdsWithSessions)
            ->with('groupe')
            ->get();

        if ($stagiaires->isEmpty()) {
            $this->warn('No stagiaires found in groups with sessions.');
            return self::SUCCESS;
        }

        $sent    = 0;
        $skipped = 0;

        foreach ($stagiaires as $stagiaire) {
            // Per-group session count (already filtered above, but double-check)
            $sessionCount = EmploiDuTemps::whereBetween('date_debut', [$nextMonday, $nextSunday])
                ->where('statut', 'actif')
                ->where('id_groupe', $stagiaire->id_groupe)
                ->count();

            if ($sessionCount === 0) {
                $skipped++;
                continue;
            }

            // Fetch distinct subjects for richer email context
            $subjects = EmploiDuTemps::whereBetween('date_debut', [$nextMonday, $nextSunday])
                ->where('statut', 'actif')
                ->where('id_groupe', $stagiaire->id_groupe)
                ->with('module')
                ->get()
                ->pluck('module.name')
                ->filter()
                ->unique()
                ->values();

            if ($dryRun) {
                $this->line("  [DRY-RUN] Would email: {$stagiaire->email} ({$sessionCount} sessions)");
            } else {
                Mail::to($stagiaire->email)
                    ->send(new WeeklyScheduleNotification(
                        stagiaire:    $stagiaire,
                        weekStart:    $nextMonday,
                        sessionCount: $sessionCount,
                        subjects:     $subjects,
                    ));
                $this->line("  ✉ Sent to {$stagiaire->email}");
            }

            $sent++;
        }

        $this->info("Done — {$sent} email(s) sent, {$skipped} skipped.");
        return self::SUCCESS;
    }
}