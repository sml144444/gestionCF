<?php

namespace App\Services;

use App\Models\AbsenceRetard;
use App\Models\Controle;
use App\Models\Note;
use Illuminate\Support\Collection;

class BulletinService
{
    /**
     * Calculate CC, moduleGrade, and EFM display value for one module / one stagiaire.
     *
     * Rules:
     *   CC          → only if ALL controles have a note (not just some)
     *   moduleGrade → only if CC is calculated AND EFM has a note
     *   efmDisplay  → raw stored value /20 (or null)
     */
    public function calculateForModule(
        Collection $controles,
        ?object    $efm,
        int        $stagiaireId
    ): array {
        // ── 1. CC — only if every controle has a note ─────────────────────
        $cc = null;

        if ($controles->isNotEmpty()) {
            $ccValues  = [];
            $allFilled = true;

            foreach ($controles as $ctrl) {
                $note = Note::where('id_user',     $stagiaireId)
                            ->where('id_controle', $ctrl->id)
                            ->value('note');

                if ($note === null) {
                    $allFilled = false;
                    break;
                }

                $ccValues[] = (float) $note;
            }

            if ($allFilled && count($ccValues) > 0) {
                $cc = round(array_sum($ccValues) / count($ccValues), 2);
            }
        }

        // ── 2. EFM ────────────────────────────────────────────────────────
        $efmNote = null;

        if ($efm) {
            $raw = Note::where('id_user',     $stagiaireId)
                       ->where('id_controle', $efm->id)
                       ->value('note');

            if ($raw !== null) {
                $efmNote = (float) $raw;
            }
        }

        // ── 3. Module grade — only if BOTH cc AND efm are present ─────────
        $moduleGrade    = null;
        $hasNoControles = $controles->isEmpty();

        if ($hasNoControles) {
            if ($efmNote !== null) {
                $moduleGrade = round($efmNote, 2);
            }
        } else {
            if ($cc !== null && $efmNote !== null) {
                $moduleGrade = round(($cc + $efmNote) / 2, 2);
            }
        }

        return [
            'cc'          => $cc,
            'efmDisplay'  => $efmNote,
            'moduleGrade' => $moduleGrade,
        ];
    }

    /**
     * Discipline note for one stagiaire.
     *
     * Rules:
     *   - Only count absences where justifie = false (unjustified),
     *     regardless of admin_validated value.
     *   - penalty = total_absence_hours / 5
     *   - discipline_note = max(0, 20 - penalty)
     */
    public function calculateDisciplineNote(int $stagiaireId): float
    {
        $totalHours = AbsenceRetard::where('id_user', $stagiaireId)
            ->where('type',     'absence')
            ->where('justifie', false)       // only unjustified absences
            ->sum('duree');

        $penalty = (float) $totalHours / 5;

        return round(max(0, 20 - $penalty), 2);
    }

    /**
     * General average across all modules that have a moduleGrade,
     * plus an optional discipline note (coefficient = 1).
     *
     * Weighted by coefficient.
     *
     * @param  array       $items            each: ['module' => Module, 'moduleGrade' => float|null]
     * @param  float|null  $disciplineNote   pass null to exclude discipline from average
     * @param  int         $disciplineCoeff  coefficient for discipline row (default 1)
     */
    public function calculateGeneralAverage(
        array  $items,
        ?float $disciplineNote  = null,
        int    $disciplineCoeff = 1
    ): ?float {
        $weightedSum    = 0;
        $coefficientSum = 0;

        foreach ($items as $item) {
            if ($item['moduleGrade'] === null) {
                continue;
            }

            $coeff           = (float) ($item['module']->coefficience ?? 1);
            $weightedSum    += $item['moduleGrade'] * $coeff;
            $coefficientSum += $coeff;
        }

        // Include discipline note in the weighted average
        if ($disciplineNote !== null) {
            $weightedSum    += $disciplineNote * $disciplineCoeff;
            $coefficientSum += $disciplineCoeff;
        }

        if ($coefficientSum === 0) {
            return null;
        }

        return round($weightedSum / $coefficientSum, 2);
    }

    /**
     * Final grade calculation.
     *
     * Not final year       → finalGrade = moyenneGenerale
     * Final year + EFF     → finalGrade = (eff × 0.6) + (moyenneGenerale × 0.4)
     * Final year, no EFF   → finalGrade = null (waiting for EFF)
     */
    public function calculateFinalGrade(
        ?float $moyenneGenerale,
        bool   $isFinalYear,
        ?float $effNote
    ): ?float {
        if (! $isFinalYear) {
            return $moyenneGenerale;
        }

        if ($effNote === null || $moyenneGenerale === null) {
            return null;
        }

        return round(($effNote * 0.6) + ($moyenneGenerale * 0.4), 2);
    }
}