<?php

namespace App\Services;

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
            $ccValues = [];
            $allFilled = true;

            foreach ($controles as $ctrl) {
                $note = Note::where('id_user',     $stagiaireId)
                            ->where('id_controle', $ctrl->id)
                            ->value('note');

                if ($note === null) {
                    $allFilled = false;
                    break; // no need to continue
                }

                $ccValues[] = (float) $note;
            }

            if ($allFilled && count($ccValues) > 0) {
                $cc = round(
                    array_sum($ccValues) / count($ccValues),
                    2
                );
            }
            // if any controle is missing → cc stays null
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
        // Special case: module has 0 controles (nbr_controles = 0)
        //   → cc is null by design but we still allow grade = EFM alone
        $moduleGrade = null;

        $hasNoControles = $controles->isEmpty();

        if ($hasNoControles) {
            // No controles configured → grade = EFM only (if EFM filled)
            if ($efmNote !== null) {
                $moduleGrade = round($efmNote, 2);
            }
        } else {
            // Normal case: need BOTH cc (all controles filled) AND efm
            if ($cc !== null && $efmNote !== null) {
                $moduleGrade = round(($cc + $efmNote) / 2, 2);
            }
            // if cc is null (some controle missing) → moduleGrade stays null
            // if efmNote is null → moduleGrade stays null
        }

        return [
            'cc'          => $cc,
            'efmDisplay'  => $efmNote,
            'moduleGrade' => $moduleGrade,
        ];
    }

    /**
     * General average across all modules that have a moduleGrade.
     * Weighted by coefficient.
     *
     * @param  array  $items  each: ['module' => Module, 'moduleGrade' => float|null]
     */
    public function calculateGeneralAverage(array $items): ?float
    {
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

        if ($coefficientSum === 0) {
            return null;
        }

        return round($weightedSum / $coefficientSum, 2);
    }
}