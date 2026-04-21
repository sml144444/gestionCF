@extends('layouts.app')
@section('title', 'News & Événements')
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
.news-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.news-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.news-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.news-hero::before { content:''; position:absolute; left:-20px; bottom:-30px; width:140px; height:140px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none; }
.news-hero-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.news-hero-title { font-size:20px; font-weight:800; color:white; margin:0; }
.news-hero-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }
.news-hero-badge { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:99px; }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.news-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(360px,1fr)); gap:20px; }
.news-card { background:white; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; transition:all .25s; display:flex; flex-direction:column; }
.news-card:hover { transform:translateY(-3px); box-shadow:0 16px 40px rgba(0,0,0,0.1); }
.news-card-img { width:100%; height:200px; object-fit:cover; }
.news-card-img-placeholder { width:100%; height:200px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 100%); }
.news-card-body { padding:20px; flex:1; display:flex; flex-direction:column; }
.news-card-meta { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.news-card-avatar { width:36px; height:36px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:white; flex-shrink:0; }
.news-card-author { font-size:12px; font-weight:700; color:#1e293b; }
.news-card-date { font-size:10px; color:#94a3b8; }
.news-card-title { font-size:16px; font-weight:800; color:#1e293b; margin:0 0 10px; line-height:1.4; }
.news-card-excerpt { font-size:13px; color:#64748b; line-height:1.6; flex:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; }
.news-card-footer { padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.news-card-actions { display:flex; gap:12px; }
.news-action-btn { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:#64748b; background:none; border:none; cursor:pointer; padding:5px 10px; border-radius:8px; transition:all .15s; text-decoration:none; }
.news-action-btn:hover { background:#f1f5f9; color:#1e293b; }
.news-action-btn.liked { color:#e11d48; }
.news-action-btn.liked svg { stroke:#e11d48; fill:#fee2e2; }
.btn-sm { font-size:11px; font-weight:600; padding:7px 14px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:all .15s; border:none; }
.btn-sm-primary { background:var(--accent-gr); color:white; box-shadow:0 2px 8px var(--accent-sh); }
.btn-sm-primary:hover { opacity:.88; }
.btn-sm-outline { background:white; border:1.5px solid #e2e8f0; color:#64748b; }
.btn-sm-outline:hover { border-color:var(--accent-bd); background:var(--accent-lt); color:var(--accent-tx); }
.btn-sm-danger { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.btn-sm-danger:hover { background:#fecaca; }
.news-empty { grid-column:1/-1; padding:64px; text-align:center; background:white; border-radius:20px; border:1px solid #e2e8f0; }
</style>

<div class="news-wrap">

{{-- FLASH --}}
@if(session('success'))
    <div class="flash-ok">
        <div class="flash-ok-icon"><svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
        <p style="font-size:12px;color:#be123c;margin:0;">✕ {{ session('error') }}</p>
    </div>
@endif

{{-- HERO --}}
<div class="news-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="news-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
        </div>
        <div>
            <h1 class="news-hero-title">News & Événements</h1>
            <p class="news-hero-sub">
                <strong style="color:white;">{{ $news->total() }}</strong> publications
            </p>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <span class="news-hero-badge">{{ ucfirst($role) }}</span>
        @can('news-create')
            <a href="{{ route('news.create') }}" class="btn-sm" style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Nouvelle publication
            </a>
        @endcan
    </div>
</div>

{{-- GRID --}}
<div class="news-grid">
@forelse($news as $item)
    @php
        $initiale = strtoupper(substr($item->auteur->name ?? '?', 0, 1));
        $isLiked  = $item->isLikedBy($user);
    @endphp
    <div class="news-card">
        {{-- IMAGE --}}
        @if($item->image)
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->titre }}" class="news-card-img">
        @else
            <div class="news-card-img-placeholder">
                <svg width="48" height="48" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        <div class="news-card-body">
            {{-- META --}}
            <div class="news-card-meta">
                <div class="news-card-avatar">{{ $initiale }}</div>
                <div>
                    <div class="news-card-author">{{ $item->auteur->name ?? 'Inconnu' }}</div>
                    <div class="news-card-date">{{ $item->created_at->diffForHumans() }}</div>
                </div>
            </div>
            {{-- TITLE --}}
            <h3 class="news-card-title">{{ $item->titre }}</h3>
            {{-- EXCERPT --}}
            <p class="news-card-excerpt">{{ strip_tags($item->contenu) }}</p>
        </div>

        <div class="news-card-footer">
            {{-- STATS --}}
            <div class="news-card-actions">
                @can('news-like')
                    <button
                        class="news-action-btn {{ $isLiked ? 'liked' : '' }}"
                        onclick="toggleLike({{ $item->id }}, this)"
                        data-liked="{{ $isLiked ? 'true' : 'false' }}">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="like-count-{{ $item->id }}">{{ $item->likes->count() }}</span>
                    </button>
                @endcan
                <span class="news-action-btn" style="cursor:default;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    {{ $item->comments->count() }}
                </span>
            </div>

            {{-- ACTIONS --}}
            <div style="display:flex;gap:6px;align-items:center;">
                <a href="{{ route('news.show', $item) }}" class="btn-sm btn-sm-outline">Lire →</a>
                @can('news-edit')
                    @if(Auth::id() === $item->id_user || Auth::user()->role === 'admin')
                        <a href="{{ route('news.edit', $item) }}" class="btn-sm btn-sm-outline">✎</a>
                    @endif
                @endcan
                @can('news-delete')
                    @if(Auth::id() === $item->id_user || Auth::user()->role === 'admin')
                        <button onclick="openDeleteModal('{{ route('news.destroy', $item) }}', '{{ addslashes($item->titre) }}')" class="btn-sm btn-sm-danger">🗑️</button>
                    @endif
                @endcan
            </div>
        </div>
    </div>
@empty
    <div class="news-empty">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--accent-lt);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
        </div>
        <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucune publication</p>
        <p style="font-size:12px;color:#94a3b8;margin:0 0 16px;">Les actualités de l'établissement apparaîtront ici.</p>
        @can('news-create')
            <a href="{{ route('news.create') }}" class="btn-sm btn-sm-primary">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Créer la première publication
            </a>
        @endcan
    </div>
@endforelse
</div>

{{-- PAGINATION --}}
@if($news->hasPages())
    <div style="margin-top:28px; display:flex; justify-content:center;">
        {{ $news->links() }}
    </div>
@endif

{{-- DELETE MODAL --}}
<div id="delete-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeDeleteModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px; margin:16px; padding:24px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; padding-bottom:14px; border-bottom:2px solid #dc2626;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <div style="font-size:15px;font-weight:800;color:#1e293b;">Supprimer la publication ?</div>
                <div style="font-size:11px;color:#64748b;" id="delete-modal-name"></div>
            </div>
            <button onclick="closeDeleteModal()" style="margin-left:auto;width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;font-size:16px;">×</button>
        </div>
        <div style="padding:12px 14px; border-radius:12px; background:#fff1f2; border:1px solid #fecdd3; font-size:12px; color:#9f1239; margin-bottom:20px;">
            Cette action est irréversible. Les commentaires et likes associés seront également supprimés.
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeDeleteModal()" class="btn-sm btn-sm-outline" style="flex:1; justify-content:center;">Annuler</button>
            <form id="delete-form" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm btn-sm-danger" style="width:100%; justify-content:center;">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>

<script>
function openDeleteModal(action, title) {
    document.getElementById('delete-form').action = action;
    document.getElementById('delete-modal-name').textContent = title;
    document.getElementById('delete-modal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
}

async function toggleLike(id, btn) {
    try {
        const res = await fetch(`/news/${id}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        btn.classList.toggle('liked', data.liked);
        btn.dataset.liked = data.liked;
        btn.querySelector('.like-count-' + id) && (btn.querySelector('.like-count-' + id).textContent = data.count);
        // Update all like-count spans for this id
        document.querySelectorAll('.like-count-' + id).forEach(el => el.textContent = data.count);
    } catch(e) { console.error(e); }
}
</script>
@endsection