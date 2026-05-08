

<?php
    $unreadCount = \App\Models\UserNotification::forUser(Auth::id())->unread()->count();
    $latest      = \App\Models\UserNotification::forUser(Auth::id())
        ->orderByDesc('updated_at')
        ->limit(5)
        ->get();
?>

<div class="relative" id="notif-wrapper">

    
    <button id="notif-bell"
            onclick="toggleNotifDropdown()"
            class="w-8 h-8 flex items-center justify-center rounded-lg
                   text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-all">
        <svg id="bell-icon" class="w-[17px] h-[17px] transition-transform"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                     a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                     C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                     L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
    </button>

    
    <span id="notif-badge"
          class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-0.5 flex items-center justify-center
                 rounded-full text-[9px] font-bold text-white bg-red-500 pointer-events-none
                 transition-all duration-200"
          style="<?php echo e($unreadCount === 0 ? 'display:none;' : ''); ?>">
        <span id="notif-badge-count"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
    </span>

    
    <div id="notif-dropdown"
         class="absolute right-0 top-10 w-80 bg-white rounded-2xl shadow-xl
                border border-slate-100 z-50 overflow-hidden"
         style="display:none; transform-origin:top right;">

        
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-xs font-bold text-slate-700">Notifications</span>
            <button onclick="markAllRead()"
                    class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                Tout marquer lu
            </button>
        </div>

        
        <div id="notif-list" class="max-h-72 overflow-y-auto divide-y divide-slate-50">
            <?php $__empty_1 = true; $__currentLoopData = $latest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $cfg = $n->type_config; ?>
                <div id="notif-item-<?php echo e($n->id); ?>"
                     class="notif-item group flex items-start gap-3 px-4 py-3
                            hover:bg-slate-50 cursor-pointer transition-colors relative
                            <?php echo e($n->is_read ? 'opacity-60' : ''); ?>"
                     data-id="<?php echo e($n->id); ?>"
                     data-url="<?php echo e($n->url ?? ''); ?>"
                     onclick="handleNotifClick(this)">

                    
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm"
                         style="background:<?php echo e($cfg['bg']); ?>;">
                        <?php echo e($cfg['icon']); ?>

                    </div>

                    
                    <div class="flex-1 min-w-0">
                        <p class="notif-message text-xs text-slate-700 leading-snug <?php echo e($n->is_read ? '' : 'font-semibold'); ?>">
                            <?php echo e($n->message); ?>

                        </p>
                        <p class="notif-time text-[10px] text-slate-400 mt-0.5">
                            <?php echo e($n->updated_at->diffForHumans()); ?>

                        </p>
                    </div>

                    
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        
                        <button onclick="deleteNotification(event, <?php echo e($n->id); ?>)"
                                class="opacity-0 group-hover:opacity-100 transition-opacity
                                       w-5 h-5 flex items-center justify-center rounded
                                       text-slate-300 hover:text-red-400 hover:bg-red-50">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <?php if($n->count > 1): ?>
                            <span class="notif-count-badge text-[9px] font-bold px-1.5 py-0.5
                                         rounded-full bg-blue-500 text-white">
                                <?php echo e($n->count); ?>

                            </span>
                        <?php elseif(! $n->is_read): ?>
                            <span class="notif-unread-dot w-2 h-2 rounded-full bg-blue-500"></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div id="notif-empty" class="text-center py-10">
                    <div class="text-3xl mb-2">🔔</div>
                    <p class="text-xs text-slate-400 font-medium">Aucune notification</p>
                </div>
            <?php endif; ?>
        </div>


<div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
    <button id="notif-load-all-btn"
            onclick="loadAllNotifications()"
            class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 transition-colors">
        Voir tout →
    </button>
    <button id="notif-collapse-btn"
            onclick="collapseNotifications()"
            class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 transition-colors"
            style="display:none;">
        Réduire ↑
    </button>
</div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('69f841fa-b640-4ef2-aadd-b076f2f1e792')): $__env->markAsRenderedOnce('69f841fa-b640-4ef2-aadd-b076f2f1e792'); ?>
<?php $__env->startPush('scripts'); ?>
<style>
@keyframes bellShake {
    0%,100%{transform:rotate(0)}
    15%{transform:rotate(12deg)}
    30%{transform:rotate(-10deg)}
    45%{transform:rotate(8deg)}
    60%{transform:rotate(-6deg)}
    75%{transform:rotate(4deg)}
}
.bell-ring { animation: bellShake .5s ease; }

@keyframes notifSlideIn {
    from { opacity:0; transform:translateY(-6px) scale(.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
#notif-dropdown.open { animation: notifSlideIn .15s ease; }

@keyframes notifItemIn {
    from { opacity:0; transform:translateX(10px); }
    to   { opacity:1; transform:translateX(0); }
}
.notif-item-new { animation: notifItemIn .25s ease; }

@keyframes countPop {
    0%  { transform: scale(1); }
    50% { transform: scale(1.4); }
    100%{ transform: scale(1); }
}
.count-pop { animation: countPop .25s ease; }

#notif-list::-webkit-scrollbar { width:4px; }
#notif-list::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:99px; }
#notif-list { transition: max-height .3s ease; }
</style>

<script>
// ── Dropdown toggle ────────────────────────────────────────────
let _notifOpen = false;

function toggleNotifDropdown() { _notifOpen ? closeNotifDropdown() : openNotifDropdown(); }

function openNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    dd.style.display = 'block';
    dd.classList.add('open');
    _notifOpen = true;
    setTimeout(() => dd.classList.remove('open'), 200);
}

function closeNotifDropdown() {
    document.getElementById('notif-dropdown').style.display = 'none';
    _notifOpen = false;
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) closeNotifDropdown();
});

// ── Badge helpers ──────────────────────────────────────────────
function getUnreadCount() {
    const el = document.getElementById('notif-badge-count');
    if (!el) return 0;
    const t = el.textContent.trim();
    return t === '9+' ? 10 : parseInt(t) || 0;
}

function setUnreadCount(n) {
    const badge = document.getElementById('notif-badge');
    const count = document.getElementById('notif-badge-count');
    if (!badge || !count) return;
    if (n <= 0) { badge.style.display = 'none'; }
    else        { badge.style.display = 'flex'; count.textContent = n > 9 ? '9+' : n; }
}

function ringBell() {
    const icon = document.getElementById('bell-icon');
    if (!icon) return;
    icon.classList.remove('bell-ring');
    void icon.offsetWidth;
    icon.classList.add('bell-ring');
    icon.addEventListener('animationend', () => icon.classList.remove('bell-ring'), { once: true });
}

// ── Play a tiny ping ───────────────────────────────────────────
function playPing() {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine'; osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.08, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
        osc.start(); osc.stop(ctx.currentTime + 0.4);
    } catch(_) {}
}

// ── Mark one as read ───────────────────────────────────────────
function handleNotifClick(el) {
    const id  = el.dataset.id;
    const url = el.dataset.url;
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        el.classList.add('opacity-60');
        el.querySelector('.notif-unread-dot')?.remove();
        el.querySelector('.notif-count-badge')?.remove();
        el.querySelector('.notif-message')?.classList.replace('font-semibold', 'font-normal');
        setUnreadCount(data.unread_count ?? Math.max(0, getUnreadCount() - 1));
        if (url) window.location.href = url;
    });
}

// ── Mark all as read ───────────────────────────────────────────
function markAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
    })
    .then(r => r.json())
    .then(() => {
        setUnreadCount(0);
        document.querySelectorAll('.notif-item').forEach(el => {
            el.classList.add('opacity-60');
            el.querySelector('.notif-unread-dot')?.remove();
            el.querySelector('.notif-count-badge')?.remove();
            el.querySelector('.notif-message')?.classList.replace('font-semibold', 'font-normal');
        });
    });
}

// ── Delete a single notification ──────────────────────────────
function deleteNotification(event, id) {
    event.stopPropagation(); // don't trigger handleNotifClick

    const item = document.getElementById('notif-item-' + id);
    if (!item) return;

    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        // Animate out
        item.style.transition = 'opacity .2s ease, transform .2s ease';
        item.style.opacity    = '0';
        item.style.transform  = 'translateX(10px)';
        setTimeout(() => {
            item.remove();
            // Show empty state if list is now empty
            const list = document.getElementById('notif-list');
            if (list && !list.querySelector('.notif-item')) {
                list.innerHTML = `
                    <div id="notif-empty" class="text-center py-10">
                        <div class="text-3xl mb-2">🔔</div>
                        <p class="text-xs text-slate-400 font-medium">Aucune notification</p>
                    </div>`;
            }
        }, 200);

        setUnreadCount(data.unread_count);
    })
    .catch(() => {});
}

// ── Prepend a brand-new notification item ─────────────────────
function prependNotification(e) {
    ringBell();
    playPing();

    document.getElementById('notif-empty')?.remove();
    const list = document.getElementById('notif-list');
    if (!list) return;

    const item = document.createElement('div');
    item.id        = 'notif-item-' + e.id;
    item.className = 'notif-item notif-item-new group flex items-start gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors';
    item.dataset.id  = e.id;
    item.dataset.url = e.url ?? '';
    item.setAttribute('onclick', 'handleNotifClick(this)');
    item.innerHTML = `
        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm"
             style="background:${e.bg};">
            ${e.icon}
        </div>
        <div class="flex-1 min-w-0">
            <p class="notif-message text-xs text-slate-700 leading-snug font-semibold">
                ${escN(e.message)}
            </p>
            <p class="notif-time text-[10px] text-slate-400 mt-0.5">${escN(e.created_at)}</p>
        </div>
        <div class="flex flex-col items-end gap-1 flex-shrink-0">
            <button onclick="deleteNotification(event, ${e.id})"
                    class="opacity-0 group-hover:opacity-100 transition-opacity
                           w-5 h-5 flex items-center justify-center rounded
                           text-slate-300 hover:text-red-400 hover:bg-red-50">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <span class="notif-unread-dot w-2 h-2 rounded-full bg-blue-500"></span>
        </div>
    `;

    list.prepend(item);
    const items = list.querySelectorAll('.notif-item');
    if (items.length > 5) items[items.length - 1].remove();

    setUnreadCount(getUnreadCount() + 1);
}

// ── Patch an EXISTING notification item (count incremented) ───
function patchNotification(e) {
    ringBell();
    playPing();

    const item = document.getElementById('notif-item-' + e.id);

    if (!item) {
        // Item not in the visible list (may have been pushed out) → prepend it fresh
        prependNotification(e);
        return;
    }

    // Update message text
    const msgEl = item.querySelector('.notif-message');
    if (msgEl) msgEl.textContent = e.message;

    // Update timestamp
    const timeEl = item.querySelector('.notif-time');
    if (timeEl) timeEl.textContent = e.created_at;

    // Update the count badge (create if missing, update if exists)
    const container = item.querySelector('.flex.flex-col.items-end.gap-1');
    if (container) {
        // Preserve the delete button
        const deleteBtn = container.querySelector('button');
        container.innerHTML = `
            ${deleteBtn ? deleteBtn.outerHTML : `
                <button onclick="deleteNotification(event, ${e.id})"
                        class="opacity-0 group-hover:opacity-100 transition-opacity
                               w-5 h-5 flex items-center justify-center rounded
                               text-slate-300 hover:text-red-400 hover:bg-red-50">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `}
            <span class="notif-count-badge count-pop text-[9px] font-bold px-1.5 py-0.5
                         rounded-full bg-blue-500 text-white">
                ${e.count}
            </span>
        `;
    }

    // Move item to top of list (most recent)
    const list = document.getElementById('notif-list');
    if (list && item.parentElement === list) list.prepend(item);

    // Bell badge stays the same (still same 1 unread notification, just updated)
    // No increment needed — it was already counted when first created
}

// ── Load ALL notifications into the dropdown ───────────────────
function loadAllNotifications() {
    const btn  = document.getElementById('notif-load-all-btn');
    const list = document.getElementById('notif-list');
    if (!btn || !list) return;

    btn.textContent = 'Chargement…';
    btn.disabled    = true;

    fetch('/notifications?all=1', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        list.innerHTML = '';

        if (!data.notifications.length) {
            list.innerHTML = `
                <div class="text-center py-10">
                    <div class="text-3xl mb-2">🔔</div>
                    <p class="text-xs text-slate-400 font-medium">Aucune notification</p>
                </div>`;
        } else {
            data.notifications.forEach(n => {
                const item = document.createElement('div');
                item.id        = 'notif-item-' + n.id;
                item.className = 'notif-item group flex items-start gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors ' + (n.is_read ? 'opacity-60' : '');
                item.dataset.id  = n.id;
                item.dataset.url = n.url ?? '';
                item.setAttribute('onclick', 'handleNotifClick(this)');
                item.innerHTML = `
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm"
                         style="background:${escN(n.bg)};">${escN(n.icon)}</div>
                    <div class="flex-1 min-w-0">
                        <p class="notif-message text-xs text-slate-700 leading-snug ${n.is_read ? 'font-normal' : 'font-semibold'}">
                            ${escN(n.message)}
                        </p>
                        <p class="notif-time text-[10px] text-slate-400 mt-0.5">${escN(n.created_at)}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <button onclick="deleteNotification(event, ${n.id})"
                                class="opacity-0 group-hover:opacity-100 transition-opacity
                                       w-5 h-5 flex items-center justify-center rounded
                                       text-slate-300 hover:text-red-400 hover:bg-red-50">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        ${n.count > 1
                            ? `<span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-500 text-white">${n.count}</span>`
                            : !n.is_read
                            ? `<span class="notif-unread-dot w-2 h-2 rounded-full bg-blue-500"></span>`
                            : ''}
                    </div>
                `;
                list.appendChild(item);
            });
        }

        // Expand the list height to show everything
       list.style.maxHeight = '70vh';

        // Swap buttons
        btn.style.display = 'none';
        document.getElementById('notif-collapse-btn').style.display = 'block';
    })
    .catch(() => {
        btn.textContent = 'Voir tout →';
        btn.disabled    = false;
    });
}

// ── Collapse back to 5 items ───────────────────────────────────
function collapseNotifications() {
    const list = document.getElementById('notif-list');
    if (list) list.style.maxHeight = '18rem';

    // Reset the load button so it works again
    const btn = document.getElementById('notif-load-all-btn');
    if (btn) {
        btn.textContent = 'Voir tout →';
        btn.disabled    = false;
        btn.style.display = 'block';
    }

    document.getElementById('notif-collapse-btn').style.display = 'none';
}

function escN(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH C:\Project\gestion-CF\resources\views/partials/notifications/bell.blade.php ENDPATH**/ ?>