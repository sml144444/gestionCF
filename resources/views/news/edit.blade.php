@extends('layouts.app')
@section('title', 'Modifier la publication')
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
.nf-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:760px; margin:0 auto; }
.nf-back { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#64748b; text-decoration:none; padding:8px 14px; border-radius:10px; background:white; border:1.5px solid #e2e8f0; margin-bottom:20px; transition:all .15s; }
.nf-back:hover { color:var(--accent-tx); border-color:var(--accent-bd); background:var(--accent-lt); }
.nf-hero { background:var(--accent-gr); border-radius:20px; padding:24px 28px; margin-bottom:24px; display:flex; align-items:center; gap:16px; position:relative; overflow:hidden; }
.nf-hero::after { content:''; position:absolute; right:-30px; top:-30px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.nf-hero-icon { width:48px; height:48px; border-radius:15px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.nf-hero-title { font-size:18px; font-weight:800; color:white; margin:0; }
.nf-hero-sub { font-size:11px; color:rgba(255,255,255,0.75); margin-top:2px; }
.nf-card { background:white; border-radius:20px; border:1px solid #e2e8f0; padding:28px 32px; }
.nf-label { display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:8px; }
.nf-label span { color:#dc2626; }
.nf-input { width:100%; padding:12px 16px; border-radius:12px; border:1.5px solid #e2e8f0; font-size:14px; font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; color:#1e293b; }
.nf-input:focus { border-color:var(--accent-bd); box-shadow:0 0 0 3px {{ $p['shadow'] }}; }
.nf-textarea { min-height:220px; resize:vertical; }
.nf-error { font-size:11px; color:#dc2626; margin-top:4px; display:block; }
.nf-field { margin-bottom:20px; }
.nf-img-current { width:100%; border-radius:14px; max-height:260px; object-fit:cover; margin-bottom:12px; }
.nf-upload-zone { border:2px dashed #e2e8f0; border-radius:16px; padding:24px; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa; }
.nf-upload-zone:hover { border-color:var(--accent-bd); background:var(--accent-ltr); }
.nf-preview { max-width:100%; border-radius:12px; margin-top:16px; display:none; max-height:260px; object-fit:cover; }
.btn-primary { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:12px; border:none; background:var(--accent-gr); color:white; font-size:14px; font-weight:700; cursor:pointer; transition:all .15s; box-shadow:0 4px 12px var(--accent-sh); }
.btn-primary:hover { opacity:.88; transform:translateY(-1px); }
.btn-outline { display:inline-flex; align-items:center; gap:6px; padding:11px 20px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; text-decoration:none; }
.btn-outline:hover { border-color:var(--accent-bd); color:var(--accent-tx); background:var(--accent-lt); }
.remove-img-btn { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:600; color:#dc2626; background:#fee2e2; border:1px solid #fecaca; padding:6px 12px; border-radius:8px; cursor:pointer; transition:all .15s; }
.remove-img-btn:hover { background:#fecaca; }
</style>

<div class="nf-wrap">
    <a href="{{ route('news.show', $news) }}" class="nf-back">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour à la publication
    </a>

    <div class="nf-hero">
        <div class="nf-hero-icon">
            <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </div>
        <div>
            <h1 class="nf-hero-title">Modifier la publication</h1>
            <p class="nf-hero-sub">{{ Str::limit($news->titre, 60) }}</p>
        </div>
    </div>

    <div class="nf-card">
        <form action="{{ route('news.update', $news) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- TITRE --}}
            <div class="nf-field">
                <label class="nf-label" for="titre">Titre <span>*</span></label>
                <input
                    type="text"
                    id="titre"
                    name="titre"
                    class="nf-input"
                    value="{{ old('titre', $news->titre) }}"
                    required>
                @error('titre') <span class="nf-error">{{ $message }}</span> @enderror
            </div>

            {{-- CONTENU --}}
            <div class="nf-field">
                <label class="nf-label" for="contenu">Contenu <span>*</span></label>
                <textarea
                    id="contenu"
                    name="contenu"
                    class="nf-input nf-textarea"
                    required>{{ old('contenu', $news->contenu) }}</textarea>
                @error('contenu') <span class="nf-error">{{ $message }}</span> @enderror
            </div>

            {{-- IMAGE --}}
            <div class="nf-field">
                <label class="nf-label">Image</label>

                @if($news->image)
                    <div id="current-img-wrap">
                        <img src="{{ Storage::url($news->image) }}" class="nf-img-current" alt="Image actuelle">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                            <span style="font-size:11px;color:#64748b;">Image actuelle</span>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="checkbox" name="remove_image" value="1" id="remove-img-cb" onchange="toggleRemove(this)">
                                <span class="remove-img-btn">✕ Supprimer l'image</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="nf-upload-zone" id="upload-zone" onclick="document.getElementById('image').click()">
                    <svg width="28" height="28" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin:0 auto 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p style="font-size:13px;font-weight:600;color:#64748b;margin:0;">{{ $news->image ? 'Remplacer l\'image' : 'Ajouter une image' }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">JPEG, PNG, WebP — max 4 Mo</p>
                </div>
                <input type="file" id="image" name="image" accept="image/*" style="display:none;" onchange="previewImage(event)">
                <img id="preview" class="nf-preview" alt="Aperçu">
                @error('image') <span class="nf-error">{{ $message }}</span> @enderror
            </div>

            {{-- SUBMIT --}}
            <div style="display:flex; gap:12px; justify-content:flex-end; padding-top:16px; border-top:1px solid #f1f5f9;">
                <a href="{{ route('news.show', $news) }}" class="btn-outline">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview = document.getElementById('preview');
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
function toggleRemove(cb) {
    const wrap = document.getElementById('current-img-wrap');
    if (wrap) wrap.style.opacity = cb.checked ? '0.4' : '1';
}
</script>
@endsection