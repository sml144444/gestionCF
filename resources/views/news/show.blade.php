@extends('layouts.app')
@section('title', $news->titre)
@section('page-title', 'News & Événements')

@section('content')
@php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
    ];
    $p = $palettes[$role] ?? $palettes['gestionnaire'];
    $initiale = strtoupper(substr($news->auteur->name ?? '?', 0, 1));
    $myInit   = strtoupper(substr($user->name ?? '?', 0, 1));
    $canEdit  = $user->can('news-edit')   && (Auth::id() === $news->id_user || $role === 'admin');
    $canDel   = $user->can('news-delete') && (Auth::id() === $news->id_user || $role === 'admin');
    $canLike  = $user->can('news-like');
    $canCmt   = $user->can('news-comment');
@endphp

<style>
:root {
    --accent:    {{ $p['primary'] }};
    --accent-md: {{ $p['medium'] }};
    --accent-lt: {{ $p['light'] }};
    --accent-ltr:{{ $p['lighter'] }};
    --accent-tx: {{ $p['text'] }};
    --accent-bd: {{ $p['border'] }};
    --accent-sh: {{ $p['shadow'] }};
    --accent-gr: {{ $p['gradient'] }};
}
* { box-sizing: border-box; }

.sp-page { font-family:'Segoe UI',system-ui,sans-serif; max-width:980px; margin:0 auto; }

/* back link */
.sp-back {
    display:inline-flex; align-items:center; gap:6px;
    font-size:12px; font-weight:600; color:#8e8e8e;
    text-decoration:none; margin-bottom:20px;
    transition:color .15s;
}
.sp-back:hover { color:#1e293b; }

/* flash */
.flash-ok {
    display:flex; align-items:center; gap:12px; padding:14px 18px;
    border-radius:14px; margin-bottom:20px;
    background:var(--accent-ltr); border:1px solid var(--accent-bd);
    animation:fadeIn .3s ease;
}
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* ── INSTAGRAM POST CARD ── */
.sp-post {
    display:flex;
    border:1px solid #dbdbdb;
    border-radius:4px;
    overflow:hidden;
    background:white;
    height:600px;              /* fixed height — Instagram style */
    max-height:90vh;
}

/* left — image */
.sp-img-side {
    flex:0 0 57%;
    background:#000;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
}
.sp-img-side img {
    width:100%; height:100%; object-fit:contain; display:block;
}
.sp-img-ph {
    width:100%; height:100%;
    background:var(--accent-gr);
    display:flex; flex-direction:column;
    align-items:center; justify-content:center; gap:12px;
}

/* right — panel */
.sp-panel {
    flex:1; display:flex; flex-direction:column;
    border-left:1px solid #dbdbdb; min-width:0; overflow:hidden;
}

/* header */
.sp-head {
    padding:14px 16px; border-bottom:1px solid #efefef;
    display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.sp-ava {
    width:36px; height:36px; border-radius:50%;
    background:var(--accent-gr);
    display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; color:white; flex-shrink:0;
}
.sp-author { font-size:13px; font-weight:700; color:#1e293b; }
.sp-time   { font-size:11px; color:#8e8e8e; }
.sp-head-actions { display:flex; gap:6px; margin-left:auto; }
.sp-edit-btn, .sp-del-btn {
    font-size:11px; font-weight:600; padding:6px 12px;
    border-radius:8px; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:4px; border:none;
}
.sp-edit-btn { background:#f1f5f9; color:#475569; }
.sp-edit-btn:hover { background:#e2e8f0; }
.sp-del-btn  { background:#fee2e2; color:#dc2626; }
.sp-del-btn:hover  { background:#fecaca; }

/* caption */
.sp-caption { padding:14px 16px; border-bottom:1px solid #efefef; flex-shrink:0; }
.sp-caption h1 { font-size:14px; font-weight:800; color:#1e293b; margin:0 0 6px; line-height:1.4; }
.sp-caption p  { font-size:13px; color:#374151; line-height:1.6; margin:0; white-space:pre-wrap; max-height:88px; overflow-y:auto; }

/* comments scrollable */
.sp-cmts { flex:1; overflow-y:auto; padding:12px 16px; }
.sp-cmt { display:flex; gap:9px; margin-bottom:14px; }
.sp-cmt-ava {
    width:30px; height:30px; border-radius:50%;
    background:linear-gradient(135deg,#64748b,#94a3b8);
    display:flex; align-items:center; justify-content:center;
    font-size:11px; font-weight:800; color:white; flex-shrink:0;
}
.sp-cmt-name { font-size:12px; font-weight:700; color:#1e293b; display:inline; margin-right:5px; }
.sp-cmt-text { font-size:12px; color:#374151; display:inline; line-height:1.5; }
.sp-cmt-foot { display:flex; align-items:center; gap:8px; margin-top:3px; }
.sp-cmt-time { font-size:10px; color:#8e8e8e; }
.sp-cmt-del  { font-size:10px; color:#dc2626; background:none; border:none; cursor:pointer; padding:0; opacity:0; transition:opacity .15s; }
.sp-cmt:hover .sp-cmt-del { opacity:1; }
.sp-no-cmt   { text-align:center; padding:32px 0; color:#8e8e8e; font-size:13px; }

/* action bar */
.sp-acts {
    padding:10px 16px; border-top:1px solid #efefef;
    display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.sp-like-btn {
    background:none; border:none; cursor:pointer; padding:0;
    display:flex; align-items:center;
    color:#1e293b; transition:color .15s;
}
.sp-like-btn.liked { color:#e11d48; }
.sp-like-btn.liked svg { stroke:#e11d48; fill:#fda4af; }
.sp-like-btn:not(.liked):hover svg { stroke:#e11d48; }
.sp-like-count { font-size:13px; font-weight:700; color:#1e293b; }
.sp-cmt-icon-btn {
    background:none; border:none; cursor:pointer; padding:0;
    color:#1e293b; display:flex; align-items:center;
    transition:opacity .15s;
}
.sp-cmt-icon-btn:hover { opacity:.6; }

/* comment input */
.sp-input-row {
    padding:10px 16px; border-top:1px solid #efefef;
    display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.sp-input {
    flex:1; border:none; outline:none;
    font-size:13px; font-family:inherit;
    color:#1e293b; background:transparent; padding:6px 0;
}
.sp-input::placeholder { color:#8e8e8e; }
.sp-send {
    background:none; border:none; cursor:pointer;
    font-size:12px; font-weight:700; color:var(--accent); padding:0;
    transition:opacity .15s;
}
.sp-send:hover { opacity:.7; }

/* ── DELETE MODAL ── */
.del-bg {
    display:none; position:fixed; inset:0; z-index:200;
    background:rgba(0,0,0,0.65); backdrop-filter:blur(4px);
    align-items:center; justify-content:center; padding:16px;
}
.del-bg.open { display:flex; animation:fadeIn .2s ease; }
.del-box {
    background:white; border-radius:16px;
    width:100%; max-width:380px; padding:24px;
}

/* ── RESPONSIVE ── */
@media(max-width:680px) {
    .sp-post { flex-direction:column; height:auto; max-height:none; }
    .sp-img-side { flex:0 0 auto; height:55vw; }
    .sp-cmts { max-height:220px; }
}
</style>

<div class="sp-page">

{{-- FLASH --}}
@if(session('success'))
    <div class="flash-ok">
        <div style="width:34px;height:34px;border-radius:50%;background:var(--accent-gr);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
    </div>
@endif

{{-- BACK --}}
<a href="{{ route('news.index') }}" class="sp-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Retour aux publications
</a>

{{-- POST — split layout --}}
<div class="sp-post">

    {{-- LEFT — image --}}
    <div class="sp-img-side">
        @if($news->image)
            <img src="{{ Storage::url($news->image) }}" alt="{{ $news->titre }}">
        @else
            <div class="sp-img-ph">
                <svg width="60" height="60" fill="none" stroke="rgba(255,255,255,0.55)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span style="color:rgba(255,255,255,0.65);font-size:13px;font-weight:600;">Pas d'image</span>
            </div>
        @endif
    </div>

    {{-- RIGHT — panel --}}
    <div class="sp-panel">

        {{-- Header --}}
        <div class="sp-head">
            <div class="sp-ava">{{ $initiale }}</div>
            <div>
                <div class="sp-author">{{ $news->auteur->name ?? 'Inconnu' }}</div>
                <div class="sp-time">{{ $news->created_at->format('d/m/Y à H:i') }} · {{ $news->created_at->diffForHumans() }}</div>
            </div>
            <div class="sp-head-actions">
                @if($canEdit)
                    <a href="{{ route('news.edit', $news) }}" class="sp-edit-btn">✎ Modifier</a>
                @endif
                @if($canDel)
                    <button onclick="openDeleteModal()" class="sp-del-btn">🗑️</button>
                @endif
            </div>
        </div>

        {{-- Caption --}}
        <div class="sp-caption">
            <h1>{{ $news->titre }}</h1>
            <p>{{ $news->contenu }}</p>
        </div>

        {{-- Comments --}}
        <div class="sp-cmts" id="comments-list">
            @forelse($news->comments as $comment)
                @php $cInit = strtoupper(substr($comment->auteur->name ?? '?', 0, 1)); @endphp
                <div class="sp-cmt">
                    <div class="sp-cmt-ava">{{ $cInit }}</div>
                    <div style="flex:1;">
                        <span class="sp-cmt-name">{{ $comment->auteur->name ?? 'Inconnu' }}</span>
                        <span class="sp-cmt-text">{{ $comment->contenu }}</span>
                        <div class="sp-cmt-foot">
                            <span class="sp-cmt-time">{{ $comment->created_at->diffForHumans() }}</span>
                            @if(Auth::id() === $comment->user_id || $user->can('news-delete'))
                                <form action="{{ route('news.comments.destroy', [$news, $comment]) }}" method="POST" onsubmit="return confirm('Supprimer ce commentaire ?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sp-cmt-del">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="sp-no-cmt" id="no-cmt-placeholder">
                    <svg width="40" height="40" fill="none" stroke="#dbdbdb" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Soyez le premier à commenter.
                </div>
            @endforelse
        </div>

        {{-- Action bar --}}
        <div class="sp-acts">
            @if($canLike)
                <button id="like-btn" class="sp-like-btn {{ $liked ? 'liked' : '' }}" onclick="toggleLike({{ $news->id }})">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            @endif
            <button class="sp-cmt-icon-btn" onclick="document.getElementById('cmt-input').focus()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </button>
            <span class="sp-like-count" id="like-count">{{ $news->likes->count() }} J'aime</span>
        </div>

        {{-- Comment input --}}
        @if($canCmt)
        <div class="sp-input-row">
            <div class="sp-ava" style="width:28px;height:28px;font-size:10px;flex-shrink:0;">{{ $myInit }}</div>
            <form action="{{ route('news.comments.store', $news) }}" method="POST" style="flex:1;display:flex;align-items:center;gap:8px;">
                @csrf
                <input
                    type="text"
                    id="cmt-input"
                    name="contenu"
                    class="sp-input"
                    placeholder="Ajouter un commentaire…"
                    required>
                <button type="submit" class="sp-send">Publier</button>
            </form>
        </div>
        @endif

    </div>{{-- end panel --}}
</div>{{-- end sp-post --}}

{{-- DELETE MODAL --}}
<div id="del-modal" class="del-bg" onclick="if(event.target===this)closeDeleteModal()">
    <div class="del-box">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #dc2626;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:800;color:#1e293b;">Supprimer cette publication ?</div>
                <div style="font-size:11px;color:#64748b;">{{ Str::limit($news->titre, 50) }}</div>
            </div>
            <button onclick="closeDeleteModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;color:#64748b;">×</button>
        </div>
        <div style="padding:10px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;font-size:12px;color:#9f1239;margin-bottom:18px;">
            Cette action est irréversible. Les commentaires et likes seront supprimés.
        </div>
        <div style="display:flex;gap:10px;">
            <button onclick="closeDeleteModal()" style="flex:1;padding:9px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;cursor:pointer;font-size:12px;font-weight:600;color:#64748b;">Annuler</button>
            <form action="{{ route('news.destroy', $news) }}" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%;padding:9px;border-radius:10px;border:none;background:#fee2e2;color:#dc2626;cursor:pointer;font-size:12px;font-weight:700;">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>{{-- end sp-page --}}

<script>
function openDeleteModal()  { document.getElementById('del-modal').classList.add('open'); }
function closeDeleteModal() { document.getElementById('del-modal').classList.remove('open'); }

async function toggleLike(id) {
    try {
        const res = await fetch('/news/' + id + '/like', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        const btn = document.getElementById('like-btn');
        if (btn) btn.classList.toggle('liked', data.liked);
        const el = document.getElementById('like-count');
        if (el) el.textContent = data.count + " J'aime";
    } catch(e) { console.error(e); }
}
</script>
@endsection