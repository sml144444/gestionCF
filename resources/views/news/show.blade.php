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
.ns-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:820px; margin:0 auto; }
.ns-back { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#64748b; text-decoration:none; padding:8px 14px; border-radius:10px; background:white; border:1.5px solid #e2e8f0; margin-bottom:20px; transition:all .15s; }
.ns-back:hover { color:var(--accent-tx); border-color:var(--accent-bd); background:var(--accent-lt); }
.ns-card { background:white; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:24px; }
.ns-image { width:100%; max-height:420px; object-fit:cover; }
.ns-body { padding:32px; }
.ns-meta { display:flex; align-items:center; gap:12px; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #f1f5f9; }
.ns-avatar { width:44px; height:44px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:800; color:white; flex-shrink:0; }
.ns-author { font-size:14px; font-weight:700; color:#1e293b; }
.ns-date { font-size:11px; color:#94a3b8; margin-top:2px; }
.ns-title { font-size:24px; font-weight:900; color:#1e293b; margin:0 0 18px; line-height:1.35; }
.ns-content { font-size:15px; color:#374151; line-height:1.75; white-space:pre-wrap; }
.ns-actions { display:flex; align-items:center; gap:12px; padding:20px 32px; border-top:1px solid #f1f5f9; }
.ns-like-btn { display:flex; align-items:center; gap:7px; font-size:13px; font-weight:700; color:#64748b; background:#f8fafc; border:1.5px solid #e2e8f0; padding:9px 18px; border-radius:99px; cursor:pointer; transition:all .2s; }
.ns-like-btn:hover { background:#fff1f2; border-color:#fecdd3; color:#e11d48; }
.ns-like-btn.liked { background:#fff1f2; border-color:#fda4af; color:#e11d48; }
.ns-like-btn.liked svg { stroke:#e11d48; fill:#fee2e2; }
.ns-share-btn { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#64748b; background:white; border:1.5px solid #e2e8f0; padding:8px 14px; border-radius:10px; cursor:pointer; transition:all .15s; }
.ns-share-btn:hover { border-color:var(--accent-bd); color:var(--accent-tx); background:var(--accent-lt); }
.btn-sm { font-size:11px; font-weight:600; padding:7px 14px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:all .15s; border:none; }
.btn-sm-primary { background:var(--accent-gr); color:white; box-shadow:0 2px 8px var(--accent-sh); }
.btn-sm-primary:hover { opacity:.88; }
.btn-sm-outline { background:white; border:1.5px solid #e2e8f0; color:#64748b; }
.btn-sm-outline:hover { border-color:var(--accent-bd); background:var(--accent-lt); color:var(--accent-tx); }
.btn-sm-danger { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.btn-sm-danger:hover { background:#fecaca; }

/* Comments */
.cmt-section { background:white; border-radius:20px; border:1px solid #e2e8f0; padding:24px; }
.cmt-header { font-size:15px; font-weight:800; color:#1e293b; margin:0 0 20px; display:flex; align-items:center; gap:8px; }
.cmt-count { font-size:11px; font-weight:700; padding:3px 10px; border-radius:99px; background:var(--accent-lt); color:var(--accent-tx); }
.cmt-form { display:flex; gap:12px; align-items:flex-start; margin-bottom:24px; }
.cmt-form-avatar { width:36px; height:36px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:white; flex-shrink:0; margin-top:2px; }
.cmt-form-input { flex:1; padding:10px 14px; border-radius:14px; border:1.5px solid #e2e8f0; font-size:13px; font-family:inherit; resize:vertical; min-height:70px; outline:none; transition:border-color .15s; }
.cmt-form-input:focus { border-color:var(--accent-bd); box-shadow:0 0 0 3px {{ $p['shadow'] }}; }
.cmt-item { display:flex; gap:12px; padding:16px 0; border-bottom:1px solid #f8fafc; }
.cmt-item:last-child { border-bottom:none; padding-bottom:0; }
.cmt-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#64748b,#94a3b8); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:white; flex-shrink:0; }
.cmt-bubble { background:#f8fafc; border-radius:0 14px 14px 14px; padding:12px 16px; flex:1; }
.cmt-name { font-size:12px; font-weight:700; color:#1e293b; margin-bottom:4px; }
.cmt-text { font-size:13px; color:#374151; line-height:1.55; }
.cmt-footer { display:flex; align-items:center; justify-content:space-between; margin-top:8px; }
.cmt-time { font-size:10px; color:#94a3b8; }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
</style>

<div class="ns-wrap">

{{-- FLASH --}}
@if(session('success'))
    <div class="flash-ok">
        <div class="flash-ok-icon"><svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
    </div>
@endif

{{-- BACK --}}
<a href="{{ route('news.index') }}" class="ns-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Retour aux publications
</a>

{{-- ARTICLE --}}
<div class="ns-card">
    @if($news->image)
        <img src="{{ Storage::url($news->image) }}" alt="{{ $news->titre }}" class="ns-image">
    @endif

    <div class="ns-body">
        {{-- META --}}
        <div class="ns-meta">
            @php $initiale = strtoupper(substr($news->auteur->name ?? '?', 0, 1)); @endphp
            <div class="ns-avatar">{{ $initiale }}</div>
            <div style="flex:1;">
                <div class="ns-author">{{ $news->auteur->name ?? 'Inconnu' }}</div>
                <div class="ns-date">{{ $news->created_at->format('d/m/Y à H:i') }} · {{ $news->created_at->diffForHumans() }}</div>
            </div>
            {{-- ADMIN ACTIONS --}}
            <div style="display:flex;gap:8px;">
                @can('news-edit')
                    @if(Auth::id() === $news->id_user || Auth::user()->role === 'admin')
                        <a href="{{ route('news.edit', $news) }}" class="btn-sm btn-sm-outline">✎ Modifier</a>
                    @endif
                @endcan
                @can('news-delete')
                    @if(Auth::id() === $news->id_user || Auth::user()->role === 'admin')
                        <button onclick="openDeleteModal()" class="btn-sm btn-sm-danger">🗑️</button>
                    @endif
                @endcan
            </div>
        </div>

        <h1 class="ns-title">{{ $news->titre }}</h1>
        <div class="ns-content">{{ $news->contenu }}</div>
    </div>

    {{-- LIKE / STATS BAR --}}
    <div class="ns-actions">
        @can('news-like')
            <button
                id="like-btn"
                class="ns-like-btn {{ $liked ? 'liked' : '' }}"
                onclick="toggleLike({{ $news->id }})">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span id="like-count">{{ $news->likes->count() }}</span> J'aime
            </button>
        @else
            <span style="font-size:13px;color:#94a3b8;display:flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="#fda4af" stroke="#e11d48" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                {{ $news->likes->count() }} J'aime
            </span>
        @endcan

        <span style="font-size:13px;color:#94a3b8;display:flex;align-items:center;gap:6px;margin-left:4px;">
            <svg width="14" height="14" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            {{ $news->comments->count() }} commentaire(s)
        </span>
    </div>
</div>

{{-- COMMENTS SECTION --}}
<div class="cmt-section">
    <h2 class="cmt-header">
        <svg width="18" height="18" fill="none" stroke="#1e293b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        Commentaires
        <span class="cmt-count">{{ $news->comments->count() }}</span>
    </h2>

    {{-- COMMENT FORM --}}
    @can('news-comment')
        <div class="cmt-form">
            @php $myInitiale = strtoupper(substr($user->name ?? '?', 0, 1)); @endphp
            <div class="cmt-form-avatar">{{ $myInitiale }}</div>
            <form action="{{ route('news.comments.store', $news) }}" method="POST" style="flex:1;display:flex;flex-direction:column;gap:8px;">
                @csrf
                <textarea
                    name="contenu"
                    class="cmt-form-input"
                    placeholder="Écrire un commentaire..."
                    required>{{ old('contenu') }}</textarea>
                @error('contenu')
                    <span style="font-size:11px;color:#dc2626;">{{ $message }}</span>
                @enderror
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn-sm btn-sm-primary">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Publier
                    </button>
                </div>
            </form>
        </div>
    @endcan

    {{-- COMMENTS LIST --}}
    @forelse($news->comments as $comment)
        @php $cInitiale = strtoupper(substr($comment->auteur->name ?? '?', 0, 1)); @endphp
        <div class="cmt-item">
            <div class="cmt-avatar">{{ $cInitiale }}</div>
            <div style="flex:1;">
                <div class="cmt-bubble">
                    <div class="cmt-name">{{ $comment->auteur->name ?? 'Inconnu' }}</div>
                    <p class="cmt-text">{{ $comment->contenu }}</p>
                </div>
                <div class="cmt-footer">
                    <span class="cmt-time">{{ $comment->created_at->diffForHumans() }}</span>
                    @if(Auth::id() === $comment->user_id || Auth::user()->can('news-delete'))
                        <form action="{{ route('news.comments.destroy', [$news, $comment]) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-sm-danger" style="padding:4px 10px;font-size:10px;" onclick="return confirm('Supprimer ce commentaire ?')">🗑️</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:32px;color:#94a3b8;">
            <p style="font-size:13px;margin:0;">Aucun commentaire pour l'instant. Soyez le premier !</p>
        </div>
    @endforelse
</div>

{{-- DELETE MODAL --}}
<div id="delete-modal" style="display:none; position:fixed; inset:0; z-index:60; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center;" onclick="if(event.target===this)closeDeleteModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px; margin:16px; padding:24px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; padding-bottom:14px; border-bottom:2px solid #dc2626;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <div style="font-size:15px;font-weight:800;color:#1e293b;">Supprimer cette publication ?</div>
                <div style="font-size:11px;color:#64748b;">{{ Str::limit($news->titre, 50) }}</div>
            </div>
            <button onclick="closeDeleteModal()" style="margin-left:auto;width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;font-size:16px;">×</button>
        </div>
        <div style="padding:12px 14px; border-radius:12px; background:#fff1f2; border:1px solid #fecdd3; font-size:12px; color:#9f1239; margin-bottom:20px;">
            Cette action est irréversible. Tous les commentaires et likes seront supprimés.
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeDeleteModal()" class="btn-sm btn-sm-outline" style="flex:1; justify-content:center;">Annuler</button>
            <form action="{{ route('news.destroy', $news) }}" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm btn-sm-danger" style="width:100%; justify-content:center;">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>

<script>
function openDeleteModal() { document.getElementById('delete-modal').style.display = 'flex'; }
function closeDeleteModal() { document.getElementById('delete-modal').style.display = 'none'; }

async function toggleLike(id) {
    try {
        const res = await fetch(`/news/${id}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        const btn = document.getElementById('like-btn');
        btn.classList.toggle('liked', data.liked);
        document.getElementById('like-count').textContent = data.count;
    } catch(e) { console.error(e); }
}
</script>
@endsection