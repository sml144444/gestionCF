


<?php $__env->startSection('title', 'Notifications'); ?>
<?php $__env->startSection('page-title', 'Historique des notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-3">

    
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-400">
            <?php if($unreadCount > 0): ?>
                <span class="font-semibold text-blue-600"><?php echo e($unreadCount); ?></span> non lue(s)
            <?php else: ?>
                Tout est lu ✓
            <?php endif; ?>
        </p>
        <div class="flex items-center gap-2">
            <?php if($unreadCount > 0): ?>
                <button onclick="markAllReadPage()"
                        class="text-[11px] font-semibold text-blue-600 hover:text-blue-800
                               px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                    Tout marquer lu
                </button>
            <?php endif; ?>
            <?php if($notifications->total() > 0): ?>
                <button onclick="deleteAllNotifs()"
                        class="text-[11px] font-semibold text-red-500 hover:text-red-700
                               px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                    Tout supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden"
         id="notif-page-list">

        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $cfg = $n->type_config; ?>
            <div id="page-notif-<?php echo e($n->id); ?>"
                 class="group flex items-start gap-3 px-5 py-4 border-b border-slate-50
                        last:border-0 hover:bg-slate-50 transition-colors
                        <?php echo e($n->is_read ? 'opacity-60' : ''); ?>">

                
                <div class="w-9 h-9 rounded-full flex items-center justify-center
                            flex-shrink-0 text-base mt-0.5"
                     style="background:<?php echo e($cfg['bg']); ?>;">
                    <?php echo e($cfg['icon']); ?>

                </div>

                
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-700 leading-snug
                               <?php echo e($n->is_read ? 'font-normal' : 'font-semibold'); ?>">
                        <?php echo e($n->message); ?>

                    </p>
                    <p class="text-[10px] text-slate-400 mt-1">
                        <?php echo e($n->created_at->diffForHumans()); ?>

                        &middot;
                        <?php echo e($n->created_at->format('d/m/Y à H:i')); ?>

                    </p>
                    <?php if($n->url): ?>
                        <a href="<?php echo e($n->url); ?>"
                           onclick="markReadAndGo(event, <?php echo e($n->id); ?>, '<?php echo e($n->url); ?>')"
                           class="inline-block mt-1.5 text-[10px] font-semibold text-blue-600
                                  hover:text-blue-800 transition-colors">
                            Voir →
                        </a>
                    <?php endif; ?>
                </div>

                
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <?php if($n->count > 1): ?>
                        <span class="text-[9px] font-bold px-1.5 py-0.5
                                     rounded-full bg-blue-500 text-white">
                            <?php echo e($n->count); ?>

                        </span>
                    <?php elseif(! $n->is_read): ?>
                        <span class="w-2 h-2 rounded-full bg-blue-500 mt-1"></span>
                    <?php endif; ?>

                    
                    <button onclick="deletePageNotif(<?php echo e($n->id); ?>)"
                            class="opacity-0 group-hover:opacity-100 transition-opacity
                                   w-6 h-6 flex items-center justify-center rounded-lg
                                   text-slate-300 hover:text-red-400 hover:bg-red-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                     01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                     00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div id="page-empty" class="text-center py-16">
                <div class="text-4xl mb-3">🔔</div>
                <p class="text-sm text-slate-400 font-medium">Aucune notification</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($notifications->hasPages()): ?>
        <div class="flex justify-center">
            <?php echo e($notifications->links('vendor.pagination.simple-tailwind')); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const _csrf = document.querySelector('meta[name="csrf-token"]').content;

// ── Mark read + navigate ───────────────────────────────────────
function markReadAndGo(e, id, url) {
    e.preventDefault();
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
    }).finally(() => { window.location.href = url; });
}

// ── Delete one ────────────────────────────────────────────────
function deletePageNotif(id) {
    const row = document.getElementById('page-notif-' + id);
    if (!row) return;

    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        row.style.transition = 'opacity .2s, transform .2s';
        row.style.opacity    = '0';
        row.style.transform  = 'translateX(12px)';
        setTimeout(() => {
            row.remove();
            checkEmpty();
        }, 200);
        if (typeof setUnreadCount === 'function') setUnreadCount(data.unread_count);
    });
}

// ── Delete all ────────────────────────────────────────────────
function deleteAllNotifs() {
    if (!confirm('Supprimer toutes les notifications ?')) return;

    fetch('/notifications', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(() => {
        document.getElementById('notif-page-list').innerHTML = `
            <div id="page-empty" class="text-center py-16">
                <div class="text-4xl mb-3">🔔</div>
                <p class="text-sm text-slate-400 font-medium">Aucune notification</p>
            </div>`;
        if (typeof setUnreadCount === 'function') setUnreadCount(0);
    });
}

// ── Mark all read ─────────────────────────────────────────────
function markAllReadPage() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(() => {
        document.querySelectorAll('#notif-page-list > div').forEach(row => {
            row.classList.add('opacity-60');
            row.querySelector('.w-2.h-2.rounded-full.bg-blue-500')?.remove();
            const msg = row.querySelector('p.font-semibold');
            if (msg) { msg.classList.replace('font-semibold', 'font-normal'); }
        });
        if (typeof setUnreadCount === 'function') setUnreadCount(0);
        // hide the toolbar button
        document.querySelectorAll('[onclick="markAllReadPage()"]')
                .forEach(b => b.remove());
    });
}

// ── Show empty state if no rows left ─────────────────────────
function checkEmpty() {
    const list = document.getElementById('notif-page-list');
    if (list && !list.querySelector('[id^="page-notif-"]')) {
        list.innerHTML = `
            <div id="page-empty" class="text-center py-16">
                <div class="text-4xl mb-3">🔔</div>
                <p class="text-sm text-slate-400 font-medium">Aucune notification</p>
            </div>`;
        if (typeof setUnreadCount === 'function') setUnreadCount(0);
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/notifications/index.blade.php ENDPATH**/ ?>