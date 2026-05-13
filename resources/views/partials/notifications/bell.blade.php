{{--
    resources/views/partials/notifications/bell.blade.php
    ── Redesigned: role-color accents, refined glass panel ──
--}}

@php
    $unreadCount = \App\Models\UserNotification::forUser(Auth::id())->unread()->count();
    $latest      = \App\Models\UserNotification::forUser(Auth::id())
        ->orderByDesc('updated_at')
        ->limit(5)
        ->get();
@endphp

<div class="relative" id="notif-wrapper">

    {{-- ── Bell button ── --}}
    <button id="notif-bell"
            onclick="toggleNotifDropdown()"
            class="notif-bell-btn w-9 h-9 flex items-center justify-center rounded-xl transition-all">
        <svg id="bell-icon" class="w-[18px] h-[18px] transition-transform"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                     a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                     C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                     L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
    </button>

    {{-- ── Unread badge ── --}}
    <span id="notif-badge"
          class="notif-badge absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1
                 flex items-center justify-center rounded-full
                 text-[9px] font-bold text-white pointer-events-none"
          style="{{ $unreadCount === 0 ? 'opacity:0;transform:scale(0);' : '' }}">
        <span id="notif-badge-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
    </span>

    {{-- ── Dropdown panel ── --}}
    {{-- FIX: removed overflow-hidden — it was clipping the list when "Voir tout" expanded it --}}
    <div id="notif-dropdown"
         class="notif-panel absolute right-0 top-12 w-[340px] rounded-2xl"
         style="display:none; transform-origin:top right;">

        {{-- Header --}}
        <div class="notif-header flex items-center justify-between px-5 py-3.5">
            <div class="flex items-center gap-2">
                <div class="notif-header-dot w-1.5 h-1.5 rounded-full"></div>
                <span class="text-[11px] font-bold text-slate-700 tracking-widest uppercase">Notifications</span>
                @if($unreadCount > 0)
                    <span class="notif-count-pill text-[9px] font-bold px-1.5 py-0.5 rounded-full text-white">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <button onclick="markAllRead()" class="notif-action-btn text-[10px] font-semibold transition-colors">
                    Tout lire
                </button>
                <span class="text-slate-200 select-none text-xs">│</span>
                <button onclick="deleteAllNotifications()" class="text-[10px] font-semibold text-slate-400 hover:text-red-400 transition-colors">
                    Effacer
                </button>
            </div>
        </div>

        {{-- List --}}
        <div id="notif-list" class="notif-list max-h-72 overflow-y-auto">
            @forelse($latest as $n)
                @php $cfg = $n->type_config; @endphp
                <div id="notif-item-{{ $n->id }}"
                     class="notif-item group flex items-start gap-3 px-4 py-3.5 cursor-pointer relative
                            {{ $n->is_read ? 'notif-read' : 'notif-unread' }}"
                     data-id="{{ $n->id }}"
                     data-url="{{ $n->url ?? '' }}"
                     onclick="handleNotifClick(this)">

                    {{-- Left accent bar for unread --}}
                    @if(!$n->is_read)
                        <div class="notif-accent-bar absolute left-0 top-3 bottom-3 w-0.5 rounded-r-full"></div>
                    @endif

                    {{-- Icon bubble --}}
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-base"
                         style="background:{{ $cfg['bg'] }};">
                        {{ $cfg['icon'] }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="notif-message text-[12px] leading-snug text-slate-700
                                  {{ $n->is_read ? 'font-normal opacity-60' : 'font-semibold' }}">
                            {{ $n->message }}
                        </p>
                        <p class="notif-time text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
                            {{ $n->updated_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Right: delete + badge/dot --}}
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0 pt-0.5">
                        <button onclick="deleteNotification(event, {{ $n->id }})"
                                class="notif-delete-btn opacity-0 group-hover:opacity-100 transition-all
                                       w-5 h-5 flex items-center justify-center rounded-lg">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        @if($n->count > 1)
                            <span class="notif-count-badge text-[9px] font-bold px-1.5 py-0.5
                                         rounded-full text-white">
                                {{ $n->count }}
                            </span>
                        @elseif(! $n->is_read)
                            <span class="notif-unread-dot w-2 h-2 rounded-full mt-1"></span>
                        @endif
                    </div>
                </div>
            @empty
                <div id="notif-empty" class="notif-empty-state flex flex-col items-center justify-center py-12 px-6">
                    <div class="notif-empty-icon w-12 h-12 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                                     a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                                     C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                                     L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-[12px] font-semibold text-slate-500">Tout est à jour</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Aucune notification pour l'instant</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="notif-footer flex items-center justify-between px-5 py-2.5">
            <button id="notif-load-all-btn"
                    onclick="loadAllNotifications()"
                    class="notif-action-btn text-[11px] font-semibold transition-colors flex items-center gap-1">
                <span>Voir tout</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <button id="notif-collapse-btn"
                    onclick="collapseNotifications()"
                    class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-1"
                    style="display:none;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                </svg>
                <span>Réduire</span>
            </button>
        </div>
    </div>

    {{-- ── Confirm modal ── --}}
    <div id="notif-confirm-modal"
         onclick="if(event.target===this) closeConfirmModal()"
         style="display:none;position:fixed;inset:0;z-index:9999;
                align-items:center;justify-content:center;
                background:rgba(15,23,42,0.5);backdrop-filter:blur(8px);">
        <div class="notif-modal-card"
             style="background:white;border-radius:20px;padding:28px;
                    width:min(340px,90vw);
                    animation:ncmIn .22s cubic-bezier(.34,1.4,.64,1);">

            <div class="notif-modal-icon w-11 h-11 rounded-2xl flex items-center justify-center mb-4 text-lg">
                🗑️
            </div>

            <p style="font-size:15px;font-weight:800;color:#1e293b;margin:0 0 6px;">
                Effacer tout ?
            </p>
            <p style="font-size:12px;color:#94a3b8;margin:0 0 22px;line-height:1.7;">
                Toutes vos notifications seront supprimées définitivement.
                Cette action est irréversible.
            </p>

            <div style="display:flex;gap:8px;">
                <button onclick="closeConfirmModal()"
                        class="notif-modal-btn-cancel flex-1 py-2.5 rounded-xl text-xs font-bold transition-all">
                    Annuler
                </button>
                <button onclick="confirmDeleteAll()"
                        class="notif-modal-btn-confirm flex-1 py-2.5 rounded-xl text-xs font-bold text-white transition-all">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

</div>{{-- end #notif-wrapper --}}

@once
@push('scripts')
<style>
/* ══ Role-color token ══════════════════════════════════════════════ */
:root {
    --rc: var(--role-color, #1a4f8a);
    --rc-10: color-mix(in srgb, var(--rc) 10%, transparent);
    --rc-15: color-mix(in srgb, var(--rc) 15%, transparent);
    --rc-20: color-mix(in srgb, var(--rc) 20%, transparent);
}

/* ══ Bell button ══════════════════════════════════════════════════ */
.notif-bell-btn {
    color: #94a3b8;
    background: transparent;
    border: 1px solid transparent;
}
.notif-bell-btn:hover {
    color: var(--rc);
    background: var(--rc-10);
    border-color: var(--rc-20);
}
.notif-bell-btn:active { transform: scale(.92); }

/* ══ Badge ════════════════════════════════════════════════════════ */
.notif-badge {
    background: var(--rc);
    transition: transform .2s cubic-bezier(.34,1.6,.64,1), opacity .2s ease;
}

/* ══ Panel ════════════════════════════════════════════════════════ */
/* FIX: overflow:hidden removed — use overflow:visible so the list
        can expand beyond the initial panel height when "Voir tout" is clicked.
        border-radius still clips corners fine without it.               */
.notif-panel {
    background: white;
    border: 1px solid #f1f5f9;
    box-shadow:
        0 0 0 1px rgba(0,0,0,0.03),
        0 4px 16px rgba(0,0,0,0.06),
        0 20px 48px rgba(0,0,0,0.10);
    overflow: visible;
    transition: max-height .3s ease;
}

/* ══ Header ═══════════════════════════════════════════════════════ */
.notif-header {
    background: #fafbfc;
    border-bottom: 1px solid #f1f5f9;
    /* Keep header corners rounded when panel overflow is visible */
    border-radius: 1rem 1rem 0 0;
}
.notif-header-dot { background: var(--rc); }
.notif-count-pill { background: var(--rc); }
.notif-action-btn { color: var(--rc); }
.notif-action-btn:hover { opacity: .75; }

/* ══ List ═════════════════════════════════════════════════════════ */
.notif-list::-webkit-scrollbar { width: 3px; }
.notif-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
.notif-list {
    transition: max-height .3s ease;
    overflow-y: auto;
}

/* ══ Items ════════════════════════════════════════════════════════ */
.notif-item {
    border-bottom: 1px solid #f8fafc;
    transition: background .12s ease;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: #fafbfc; }
.notif-unread { background: color-mix(in srgb, var(--rc) 3%, white); }
.notif-unread:hover { background: color-mix(in srgb, var(--rc) 5%, white); }

.notif-accent-bar { background: var(--rc); }
.notif-unread-dot { background: var(--rc); }
.notif-count-badge { background: var(--rc); }

/* ══ Delete button ════════════════════════════════════════════════ */
.notif-delete-btn {
    color: #cbd5e1;
    background: transparent;
}
.notif-delete-btn:hover {
    color: #ef4444;
    background: #fff1f2;
}

/* ══ Empty state ══════════════════════════════════════════════════ */
.notif-empty-state { background: #fafbfc; }
.notif-empty-icon {
    background: var(--rc-10);
    color: var(--rc);
}

/* ══ Footer ═══════════════════════════════════════════════════════ */
.notif-footer {
    background: #fafbfc;
    border-top: 1px solid #f1f5f9;
    /* Keep footer corners rounded */
    border-radius: 0 0 1rem 1rem;
}

/* ══ Modal ════════════════════════════════════════════════════════ */
.notif-modal-card { border: 1px solid #f1f5f9; }
.notif-modal-icon { background: #fff1f2; }
.notif-modal-btn-cancel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
}
.notif-modal-btn-cancel:hover { background: #f1f5f9; }
.notif-modal-btn-confirm { background: #ef4444; border: none; }
.notif-modal-btn-confirm:hover { opacity: .87; }

/* ══ Animations ═══════════════════════════════════════════════════ */
@keyframes bellShake {
    0%,100%{ transform:rotate(0) }
    15%{ transform:rotate(12deg) }
    30%{ transform:rotate(-10deg) }
    45%{ transform:rotate(8deg) }
    60%{ transform:rotate(-6deg) }
    75%{ transform:rotate(4deg) }
}
.bell-ring { animation: bellShake .5s ease; }

@keyframes notifSlideIn {
    from { opacity:0; transform:translateY(-8px) scale(.96); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
#notif-dropdown.open { animation: notifSlideIn .18s cubic-bezier(.34,1.2,.64,1); }

@keyframes notifItemIn {
    from { opacity:0; transform:translateX(12px); }
    to   { opacity:1; transform:translateX(0); }
}
.notif-item-new { animation: notifItemIn .25s ease; }

@keyframes countPop {
    0%  { transform:scale(1); }
    50% { transform:scale(1.45); }
    100%{ transform:scale(1); }
}
.count-pop { animation: countPop .25s ease; }

@keyframes ncmIn {
    from { opacity:0; transform:translateY(14px) scale(.95); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}

@keyframes badgePop {
    0%  { transform:scale(0); opacity:0; }
    60% { transform:scale(1.3); opacity:1; }
    100%{ transform:scale(1); opacity:1; }
}
.badge-pop { animation: badgePop .3s cubic-bezier(.34,1.6,.64,1); }
</style>

<script>
/* ── Dropdown toggle ─────────────────────────────────────── */
let _notifOpen = false;

function toggleNotifDropdown() { _notifOpen ? closeNotifDropdown() : openNotifDropdown(); }

function openNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    dd.style.display = 'block';
    dd.classList.add('open');
    _notifOpen = true;
    setTimeout(() => dd.classList.remove('open'), 220);
}

function closeNotifDropdown() {
    document.getElementById('notif-dropdown').style.display = 'none';
    _notifOpen = false;
}

document.addEventListener('click', function(e) {
    const w = document.getElementById('notif-wrapper');
    // FIX: if the clicked element was removed from the DOM by its own handler
    // (e.g. btn.innerHTML swap in loadAllNotifications), e.target becomes a
    // detached node — document.contains() returns false for it, so we'd
    // wrongly close the dropdown. Guard against that here.
    if (!document.contains(e.target)) return;
    if (w && !w.contains(e.target)) closeNotifDropdown();
});

/* ── Badge helpers ──────────────────────────────────────── */
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
    if (n <= 0) {
        badge.style.opacity   = '0';
        badge.style.transform = 'scale(0)';
    } else {
        badge.style.opacity   = '1';
        badge.style.transform = 'scale(1)';
        count.textContent = n > 9 ? '9+' : n;
        badge.classList.remove('badge-pop');
        void badge.offsetWidth;
        badge.classList.add('badge-pop');
    }
}

function ringBell() {
    const icon = document.getElementById('bell-icon');
    if (!icon) return;
    icon.classList.remove('bell-ring');
    void icon.offsetWidth;
    icon.classList.add('bell-ring');
    icon.addEventListener('animationend', () => icon.classList.remove('bell-ring'), { once: true });
}

/* ── Ping sound ─────────────────────────────────────────── */
function playPing() {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine'; osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.07, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
        osc.start(); osc.stop(ctx.currentTime + 0.4);
    } catch(_) {}
}

/* ── Mark one as read ───────────────────────────────────── */
function handleNotifClick(el) {
    const id  = el.dataset.id;
    const url = el.dataset.url;
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        el.classList.remove('notif-unread');
        el.classList.add('notif-read');
        el.querySelector('.notif-accent-bar')?.remove();
        el.querySelector('.notif-unread-dot')?.remove();
        el.querySelector('.notif-count-badge')?.remove();
        const msg = el.querySelector('.notif-message');
        if (msg) { msg.classList.remove('font-semibold'); msg.classList.add('font-normal', 'opacity-60'); }
        setUnreadCount(data.unread_count ?? Math.max(0, getUnreadCount() - 1));
        if (url) window.location.href = url;
    });
}

/* ── Mark all as read ───────────────────────────────────── */
function markAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(() => {
        setUnreadCount(0);
        document.querySelectorAll('.notif-item').forEach(el => {
            el.classList.remove('notif-unread');
            el.classList.add('notif-read');
            el.querySelector('.notif-accent-bar')?.remove();
            el.querySelector('.notif-unread-dot')?.remove();
            el.querySelector('.notif-count-badge')?.remove();
            const msg = el.querySelector('.notif-message');
            if (msg) { msg.classList.remove('font-semibold'); msg.classList.add('font-normal', 'opacity-60'); }
        });
    });
}

/* ── Delete single ──────────────────────────────────────── */
function deleteNotification(event, id) {
    event.stopPropagation();
    const item = document.getElementById('notif-item-' + id);
    if (!item) return;

    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        item.style.transition = 'opacity .2s ease, transform .2s ease, max-height .25s ease';
        item.style.opacity    = '0';
        item.style.transform  = 'translateX(12px)';
        item.style.maxHeight  = item.offsetHeight + 'px';
        setTimeout(() => { item.style.maxHeight = '0'; item.style.paddingTop = '0'; item.style.paddingBottom = '0'; }, 50);
        setTimeout(() => {
            item.remove();
            const list = document.getElementById('notif-list');
            if (list && !list.querySelector('.notif-item')) {
                list.innerHTML = _emptyHtml();
            }
        }, 280);
        setUnreadCount(data.unread_count);
    })
    .catch(() => {});
}

/* ── Confirm modal ──────────────────────────────────────── */
function deleteAllNotifications() {
    document.getElementById('notif-confirm-modal').style.display = 'flex';
}
function closeConfirmModal() {
    document.getElementById('notif-confirm-modal').style.display = 'none';
}
function confirmDeleteAll() {
    closeConfirmModal();
    fetch('/notifications', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(() => {
        const items = document.querySelectorAll('.notif-item');
        items.forEach((item, i) => {
            item.style.transition = `opacity .18s ease ${i * 35}ms, transform .18s ease ${i * 35}ms`;
            item.style.opacity    = '0';
            item.style.transform  = 'translateX(12px)';
        });
        setTimeout(() => {
            const list = document.getElementById('notif-list');
            if (list) list.innerHTML = _emptyHtml();
            setUnreadCount(0);
        }, items.length * 35 + 220);
    })
    .catch(() => {});
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeConfirmModal(); });

/* ── Empty state HTML ───────────────────────────────────── */
function _emptyHtml() {
    return `<div id="notif-empty" class="notif-empty-state flex flex-col items-center justify-center py-12 px-6">
        <div class="notif-empty-icon w-12 h-12 rounded-2xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                         a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                         C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                         L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <p class="text-[12px] font-semibold text-slate-500">Tout est à jour</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Aucune notification pour l'instant</p>
    </div>`;
}

/* ── Item HTML builder ──────────────────────────────────── */
function _buildItem(e, isNew = false) {
    const cls = isNew ? 'notif-item-new' : '';
    return `
    <div id="notif-item-${e.id}"
         class="notif-item notif-unread ${cls} group flex items-start gap-3 px-4 py-3.5 cursor-pointer relative"
         data-id="${e.id}" data-url="${e.url ?? ''}" onclick="handleNotifClick(this)">
        <div class="notif-accent-bar absolute left-0 top-3 bottom-3 w-0.5 rounded-r-full"></div>
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-base"
             style="background:${e.bg};">${e.icon}</div>
        <div class="flex-1 min-w-0 pt-0.5">
            <p class="notif-message text-[12px] leading-snug text-slate-700 font-semibold">
                ${escN(e.message)}
            </p>
            <p class="notif-time text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
                ${escN(e.created_at)}
            </p>
        </div>
        <div class="flex flex-col items-end gap-1.5 flex-shrink-0 pt-0.5">
            <button onclick="deleteNotification(event, ${e.id})"
                    class="notif-delete-btn opacity-0 group-hover:opacity-100 transition-all
                           w-5 h-5 flex items-center justify-center rounded-lg">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <span class="notif-unread-dot w-2 h-2 rounded-full mt-1"></span>
        </div>
    </div>`;
}

/* ── Prepend new notification ───────────────────────────── */
function prependNotification(e) {
    ringBell(); playPing();
    document.getElementById('notif-empty')?.remove();
    const list = document.getElementById('notif-list');
    if (!list) return;
    const wrapper = document.createElement('div');
    wrapper.innerHTML = _buildItem(e, true);
    list.prepend(wrapper.firstElementChild);
    const items = list.querySelectorAll('.notif-item');
    if (items.length > 5) items[items.length - 1].remove();
    setUnreadCount(getUnreadCount() + 1);
}

/* ── Patch existing notification ────────────────────────── */
function patchNotification(e) {
    ringBell(); playPing();
    const item = document.getElementById('notif-item-' + e.id);
    if (!item) { prependNotification(e); return; }

    const msgEl = item.querySelector('.notif-message');
    if (msgEl) msgEl.textContent = e.message;
    const timeEl = item.querySelector('.notif-time');
    if (timeEl) timeEl.innerHTML = `<span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span> ${escN(e.created_at)}`;

    const container = item.querySelector('.flex.flex-col.items-end');
    if (container) {
        const deleteBtn = container.querySelector('button');
        container.innerHTML = `
            ${deleteBtn ? deleteBtn.outerHTML : `
                <button onclick="deleteNotification(event, ${e.id})"
                        class="notif-delete-btn opacity-0 group-hover:opacity-100 transition-all
                               w-5 h-5 flex items-center justify-center rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`}
            <span class="notif-count-badge count-pop text-[9px] font-bold px-1.5 py-0.5 rounded-full text-white">
                ${e.count}
            </span>`;
    }

    const list = document.getElementById('notif-list');
    if (list && item.parentElement === list) list.prepend(item);
}

/* ── Load all ───────────────────────────────────────────── */
// FIX: also expand the panel itself (not just the inner list) so content is visible
function loadAllNotifications() {
    const btn   = document.getElementById('notif-load-all-btn');
    const list  = document.getElementById('notif-list');
    const panel = document.getElementById('notif-dropdown');
    if (!btn || !list || !panel) return;

    btn.innerHTML = '<span>Chargement…</span>';
    btn.disabled  = true;

    fetch('/notifications?all=1', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        list.innerHTML = '';
        if (!data.notifications.length) {
            list.innerHTML = _emptyHtml();
        } else {
            data.notifications.forEach(n => {
                const item = document.createElement('div');
                item.id        = 'notif-item-' + n.id;
                item.className = `notif-item group flex items-start gap-3 px-4 py-3.5 cursor-pointer relative ${n.is_read ? 'notif-read' : 'notif-unread'}`;
                item.dataset.id  = n.id;
                item.dataset.url = n.url ?? '';
                item.setAttribute('onclick', 'handleNotifClick(this)');
                item.innerHTML = `
                    ${!n.is_read ? '<div class="notif-accent-bar absolute left-0 top-3 bottom-3 w-0.5 rounded-r-full"></div>' : ''}
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-base"
                         style="background:${escN(n.bg)};">${escN(n.icon)}</div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="notif-message text-[12px] leading-snug text-slate-700 ${n.is_read ? 'font-normal opacity-60' : 'font-semibold'}">
                            ${escN(n.message)}
                        </p>
                        <p class="notif-time text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
                            ${escN(n.created_at)}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0 pt-0.5">
                        <button onclick="deleteNotification(event, ${n.id})"
                                class="notif-delete-btn opacity-0 group-hover:opacity-100 transition-all
                                       w-5 h-5 flex items-center justify-center rounded-lg">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        ${n.count > 1
                            ? `<span class="notif-count-badge text-[9px] font-bold px-1.5 py-0.5 rounded-full text-white">${n.count}</span>`
                            : !n.is_read
                            ? `<span class="notif-unread-dot w-2 h-2 rounded-full mt-1"></span>`
                            : ''}
                    </div>`;
                list.appendChild(item);
            });
        }

        // FIX: expand both list AND panel
        list.style.maxHeight  = '60vh';
        list.style.overflowY  = 'auto';
        panel.style.maxHeight = '80vh';

        btn.style.display = 'none';
        document.getElementById('notif-collapse-btn').style.display = 'flex';
    })
    .catch(() => {
        btn.innerHTML = '<span>Voir tout</span><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>';
        btn.disabled  = false;
    });
}

/* ── Collapse ───────────────────────────────────────────── */
// FIX: also reset the panel max-height when collapsing
function collapseNotifications() {
    const list  = document.getElementById('notif-list');
    const panel = document.getElementById('notif-dropdown');
    if (list)  { list.style.maxHeight = '18rem'; }
    if (panel) { panel.style.maxHeight = ''; }   // reset panel to natural height

    const btn = document.getElementById('notif-load-all-btn');
    if (btn) {
        btn.innerHTML = '<span>Voir tout</span><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>';
        btn.disabled  = false;
        btn.style.display = 'flex';
    }
    document.getElementById('notif-collapse-btn').style.display = 'none';
}

function escN(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
@endpush
@endonce