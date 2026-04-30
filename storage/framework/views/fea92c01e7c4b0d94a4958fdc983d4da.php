

<?php $__env->startSection('title', 'Bulletins'); ?>
<?php $__env->startSection('page-title', 'Bulletins'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'text' => '#1e293b', 'shadow' => 'rgba(30,41,59,0.2)'],
        'formateur'    => ['primary' => '#1d4ed8', 'light' => '#eff6ff', 'text' => '#1e40af', 'shadow' => 'rgba(29,78,216,0.2)'],
    ];
    $p      = $palettes[Auth::user()->role] ?? $palettes['gestionnaire'];
    $accent = $p['primary'];
    $light  = $p['light'];
    $text   = $p['text'];
    $shadow = $p['shadow'];
?>

<style>
.bl-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.bl-card { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:20px; }
.bl-card-head { padding:16px 20px;background:#f8fafc;border-bottom:1px solid #f1f5f9; }
.bl-filter-bar { display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #f1f5f9; }
.bl-label { display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:6px; }
.bl-select { height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;font-size:12px;color:#1e293b;outline:none;transition:border-color .15s;cursor:pointer; }
.bl-select:focus { border-color:<?php echo e($accent); ?>;background:white; }
.bl-btn { display:inline-flex;align-items:center;gap:6px;padding:9px 18px;font-size:12px;font-weight:700;border-radius:10px;border:none;cursor:pointer;transition:opacity .15s;text-decoration:none; }
.bl-btn:hover { opacity:.87; }
.bl-btn-primary { background:<?php echo e($accent); ?>;color:white;box-shadow:0 4px 14px <?php echo e($shadow); ?>; }
.bl-btn-ghost   { background:white;border:1.5px solid #e2e8f0;color:#475569; }

/* Stagiaire list */
.stag-item { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f8fafc;transition:background .12s; }
.stag-item:last-child { border-bottom:none; }
.stag-item:hover { background:#fafbff; }
.stag-avatar { width:36px;height:36px;border-radius:50%;background:<?php echo e($light); ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:<?php echo e($text); ?>;flex-shrink:0; }

/* Loading spinner shown during auto-submit */
.bl-spinner { display:none;width:16px;height:16px;border:2px solid #e2e8f0;border-top-color:<?php echo e($accent); ?>;border-radius:50%;animation:spin .6s linear infinite;margin-left:6px; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<div class="bl-wrap">


<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Bulletins de notes</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Recherchez un stagiaire par groupe pour consulter son bulletin</p>
    </div>
</div>


<div class="bl-card">
    <div class="bl-card-head">
        <div style="font-size:13px;font-weight:800;color:#0f172a;">Étape 1 — Sélectionner un groupe</div>
        <div style="font-size:11px;color:#64748b;margin-top:2px;">Filtrez par filière et promotion pour affiner la liste</div>
    </div>

    
    <div class="bl-filter-bar">

        
        <div>
            <label class="bl-label">Filière</label>
            <div style="display:flex;align-items:center;">
                <select id="filiere-filter" class="bl-select" style="min-width:200px;"
                        onchange="autoSubmitFilter()">
                    <option value="">📚 Toutes filières</option>
                    <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($f->id); ?>" <?php echo e($filiereFilter == $f->id ? 'selected' : ''); ?>>
                            <?php echo e($f->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div id="spinner-filiere" class="bl-spinner"></div>
            </div>
        </div>

        
        <div>
            <label class="bl-label">Promotion</label>
            <div style="display:flex;align-items:center;">
                <select id="promo-filter" class="bl-select" style="min-width:160px;"
                        onchange="autoSubmitFilter()">
                    <option value="">🎓 Toutes promos</option>
                    <?php $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pr); ?>" <?php echo e($promoFilter == $pr ? 'selected' : ''); ?>>
                            Promo <?php echo e($pr); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div id="spinner-promo" class="bl-spinner"></div>
            </div>
        </div>

        
        <div id="filter-badges" style="display:flex;align-items:flex-end;gap:6px;padding-bottom:2px;"></div>
    </div>

    
    <div style="padding:16px 20px;">
        <form method="GET" action="<?php echo e(route('bulletin.index')); ?>"
              id="groupe-form"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

            
            <input type="hidden" name="filiere_id" id="filiere-hidden" value="<?php echo e($filiereFilter); ?>">
            <input type="hidden" name="promo"      id="promo-hidden"   value="<?php echo e($promoFilter); ?>">

            <div style="flex:1;min-width:220px;">
                <label class="bl-label">Groupe</label>
                <select name="groupe_id" id="groupe-select" class="bl-select" style="width:100%;">
                    <option value="">— Choisir un groupe —</option>
                    <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>"
                                data-filiere="<?php echo e($g->id_filiere); ?>"
                                data-promo="<?php echo e($g->promo); ?>"
                                <?php echo e(optional($selectedGroupe)->id == $g->id ? 'selected' : ''); ?>>
                            <?php echo e($g->name); ?>

                            <?php if($g->promo): ?> (<?php echo e($g->promo); ?>) <?php endif; ?>
                            · <?php echo e($g->stagiaires_count); ?> stagiaire<?php echo e($g->stagiaires_count != 1 ? 's' : ''); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div id="no-groupe-msg" style="display:none;font-size:11px;color:#f59e0b;margin-top:5px;">
                    ⚠️ Aucun groupe ne correspond aux filtres sélectionnés.
                </div>
            </div>

            <button type="submit" class="bl-btn bl-btn-primary">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Afficher
            </button>

            <?php if($filiereFilter || $promoFilter || $selectedGroupe): ?>
            <a href="<?php echo e(route('bulletin.index')); ?>" class="bl-btn bl-btn-ghost">
                Réinitialiser
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>


<?php if($selectedGroupe): ?>
<div class="bl-card">
    <div class="bl-card-head" style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:13px;font-weight:800;color:#0f172a;">
                Étape 2 — Choisir un stagiaire
            </div>
            <div style="font-size:11px;color:#64748b;margin-top:2px;">
                Groupe <strong><?php echo e($selectedGroupe->name); ?></strong>
                · <?php echo e($stagiaires->count()); ?> stagiaire<?php echo e($stagiaires->count() > 1 ? 's' : ''); ?>

            </div>
        </div>
    </div>

    
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;">
        <input type="text" id="stag-search" placeholder="Rechercher un stagiaire…"
               class="bl-select" style="max-width:360px;width:100%;"
               oninput="filterStagiaires(this.value)">
    </div>

    <?php if($stagiaires->isEmpty()): ?>
        <div style="padding:48px 32px;text-align:center;color:#94a3b8;font-size:13px;">
            Aucun stagiaire dans ce groupe.
        </div>
    <?php else: ?>
    <div id="stag-list">
        <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="stag-item" data-name="<?php echo e(strtolower($s->name)); ?>">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="stag-avatar">
                    <?php echo e(strtoupper(substr($s->name, 0, 1))); ?>

                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1e293b;"><?php echo e($s->name); ?></div>
                    <div style="font-size:11px;color:#94a3b8;"><?php echo e($s->email); ?></div>
                </div>
            </div>
            <a href="<?php echo e(route('bulletin.show', array_filter([
                           $s->id,
                           'groupe_id'  => $selectedGroupe->id,
                           'filiere_id' => $filiereFilter ?: null,
                           'promo'      => $promoFilter ?: null,
                       ]))); ?>"
               class="bl-btn bl-btn-primary" style="font-size:11px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Voir bulletin
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div id="stag-empty" style="display:none;padding:32px;text-align:center;color:#94a3b8;font-size:13px;">
            Aucun résultat pour cette recherche.
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

</div>

<script>
// ── Auto-submit when filière or promo changes ────────────────────
function autoSubmitFilter() {
    const filiereVal = document.getElementById('filiere-filter').value;
    const promoVal   = document.getElementById('promo-filter').value;

    // Sync hidden inputs
    document.getElementById('filiere-hidden').value = filiereVal;
    document.getElementById('promo-hidden').value   = promoVal;

    // Reset groupe so stale selection is not carried over
    document.getElementById('groupe-select').value = '';

    // Show spinner on whichever changed
    document.getElementById('spinner-filiere').style.display = 'block';
    document.getElementById('spinner-promo').style.display   = 'block';

    // Submit — server returns filtered groupes list
    document.getElementById('groupe-form').submit();
}

// ── Client-side cascade (still useful for badge display on load) ─
function cascadeFilter() {
    const filiereVal = document.getElementById('filiere-filter').value;
    const promoVal   = document.getElementById('promo-filter').value;

    document.getElementById('filiere-hidden').value = filiereVal;
    document.getElementById('promo-hidden').value   = promoVal;

    const select = document.getElementById('groupe-select');
    const noMsg  = document.getElementById('no-groupe-msg');
    let visible  = 0;

    [...select.options].forEach(opt => {
        if (!opt.value) return;
        const fMatch = !filiereVal || opt.dataset.filiere === filiereVal;
        const pMatch = !promoVal   || opt.dataset.promo   === promoVal;
        const show   = fMatch && pMatch;
        opt.style.display = show ? '' : 'none';
        opt.disabled      = !show;
        if (!show && opt.selected) { opt.selected = false; select.value = ''; }
        if (show) visible++;
    });

    const placeholder = select.options[0];
    if (placeholder && !placeholder.value) {
        placeholder.text = visible === 0
            ? '— Aucun groupe pour ces filtres —'
            : `— Choisir un groupe (${visible} disponible${visible > 1 ? 's' : ''}) —`;
    }

    noMsg.style.display = visible === 0 ? '' : 'none';

    updateBadges(filiereVal, promoVal);
}

function updateBadges(filiereVal, promoVal) {
    const container = document.getElementById('filter-badges');
    container.innerHTML = '';

    if (filiereVal) {
        const sel   = document.getElementById('filiere-filter');
        const label = sel.options[sel.selectedIndex].text;
        container.innerHTML += `
            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;
                         font-size:10px;font-weight:700;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
                📚 ${label}
                <button type="button" onclick="clearFilter('filiere')"
                        style="border:none;background:none;cursor:pointer;color:#6b7280;font-size:13px;line-height:1;padding:0;">×</button>
            </span>`;
    }
    if (promoVal) {
        container.innerHTML += `
            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;
                         font-size:10px;font-weight:700;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
                🎓 Promo ${promoVal}
                <button type="button" onclick="clearFilter('promo')"
                        style="border:none;background:none;cursor:pointer;color:#6b7280;font-size:13px;line-height:1;padding:0;">×</button>
            </span>`;
    }
}

function clearFilter(type) {
    if (type === 'filiere') document.getElementById('filiere-filter').value = '';
    if (type === 'promo')   document.getElementById('promo-filter').value   = '';
    // Trigger auto-submit so server refreshes the groupes list too
    autoSubmitFilter();
}

// ── Live stagiaire search ────────────────────────────────────────
function filterStagiaires(q) {
    const items = document.querySelectorAll('#stag-list .stag-item');
    const empty = document.getElementById('stag-empty');
    let visible = 0;
    items.forEach(item => {
        const match = item.dataset.name.includes(q.toLowerCase());
        item.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    if (empty) empty.style.display = visible === 0 ? '' : 'none';
}

// ── On load: render badges from server-side selections ───────────
document.addEventListener('DOMContentLoaded', function () {
    const filiereVal = document.getElementById('filiere-filter').value;
    const promoVal   = document.getElementById('promo-filter').value;
    updateBadges(filiereVal, promoVal);
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/bulletin/index.blade.php ENDPATH**/ ?>