{{-- resources/views/gestionnaire/edu-import.blade.php --}}
@extends('layouts.app')
@section('title', 'Import EDU')
@section('page-title', 'Import EDU')

@section('content')

@php
    $user = Auth::user();
    $role = $user->role;

    $palettes = [
        'admin'        => [
            'primary'  => '#0a6640',
            'medium'   => '#1a8c56',
            'light'    => '#e8f5ee',
            'lighter'  => '#f0fdf4',
            'text'     => '#065f38',
            'border'   => '#bbf7d0',
            'shadow'   => 'rgba(10,102,64,0.15)',
            'gradient' => 'linear-gradient(135deg, #0a6640 0%, #1a8c56 100%)',
        ],
        'gestionnaire' => [
            'primary'  => '#1e293b',
            'medium'   => '#334155',
            'light'    => '#f1f5f9',
            'lighter'  => '#f8fafc',
            'text'     => '#1e293b',
            'border'   => '#cbd5e1',
            'shadow'   => 'rgba(30,41,59,0.15)',
            'gradient' => 'linear-gradient(135deg, #1e293b 0%, #334155 100%)',
        ],
        'formateur'    => [
            'primary'  => '#1a4f8a',
            'medium'   => '#2563eb',
            'light'    => '#eff6ff',
            'lighter'  => '#f0f7ff',
            'text'     => '#1e40af',
            'border'   => '#bfdbfe',
            'shadow'   => 'rgba(26,79,138,0.15)',
            'gradient' => 'linear-gradient(135deg, #1a4f8a 0%, #2563eb 100%)',
        ],
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

.edu-wrap {
    font-family: 'Segoe UI', system-ui, sans-serif;
    max-width: 880px;
    margin: 0 auto;
}

/* ── Page hero ── */
.edu-hero {
    background: var(--accent-gr);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.edu-hero::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    pointer-events: none;
}
.edu-hero::before {
    content: '';
    position: absolute;
    right: 60px; bottom: -60px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}
.edu-hero-icon {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.edu-hero-title {
    font-size: 20px; font-weight: 800;
    color: white; margin: 0;
}
.edu-hero-sub {
    font-size: 12px; color: rgba(255,255,255,0.75);
    margin-top: 3px;
}
.edu-hero-badge {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    font-size: 11px; font-weight: 700;
    padding: 6px 14px; border-radius: 99px;
    white-space: nowrap;
}

/* ── Flash messages ── */
.flash-success {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px; border-radius: 14px; margin-bottom: 18px;
    background: var(--accent-ltr);
    border: 1px solid var(--accent-bd);
    animation: fadeIn .3s ease;
}
.flash-success-icon {
    width: 38px; height: 38px; border-radius: 50%;
    background: var(--accent-gr);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

@keyframes fadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

/* ── Tabs ── */
.edu-tabs {
    display: flex;
    background: white;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 5px;
    margin-bottom: 22px;
    gap: 4px;
    overflow-x: auto;
}
.edu-tab {
    flex: 1;
    min-width: 100px;
    padding: 9px 14px;
    font-size: 12px; font-weight: 600;
    border: none; border-radius: 10px;
    cursor: pointer; transition: all .2s;
    background: transparent; color: #64748b;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    white-space: nowrap;
}
.edu-tab.active {
    background: var(--accent-gr);
    color: white;
    box-shadow: 0 4px 12px var(--accent-sh);
}
.edu-tab:not(.active):hover {
    background: var(--accent-lt);
    color: var(--accent-tx);
}

/* ── Cards ── */
.edu-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 16px;
    transition: box-shadow .2s;
}
.edu-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.edu-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.edu-card-body { padding: 20px 22px; }
.edu-card-title { font-size: 13px; font-weight: 800; color: #1e293b; margin: 0; }
.edu-card-sub   { font-size: 11px; color: #64748b; margin-top: 2px; }

/* ── Upload zone ── */
.upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    padding: 36px 24px;
    cursor: pointer;
    background: #fafcff;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    transition: all .2s;
    text-align: center;
}
.upload-zone:hover, .upload-zone.drag-over {
    border-color: var(--accent);
    background: var(--accent-lt);
}
.upload-zone-icon {
    width: 60px; height: 60px; border-radius: 18px;
    background: var(--accent-lt);
    border: 1px solid var(--accent-bd);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}

/* ── Steps ── */
.edu-steps {
    display: flex; align-items: center;
    padding: 0 4px; margin-bottom: 24px;
}
.edu-step {
    display: flex; flex-direction: column; align-items: center;
    flex: 1; position: relative;
}
.edu-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 16px; left: calc(50% + 18px);
    right: calc(-50% + 18px);
    height: 2px;
    background: #e2e8f0;
    transition: background .3s;
}
.edu-step.done::after { background: var(--accent); }
.edu-step-circle {
    width: 32px; height: 32px; border-radius: 50%;
    font-size: 12px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    z-index: 1; transition: all .3s;
    background: #e2e8f0; color: #94a3b8;
}
.edu-step.active .edu-step-circle {
    background: var(--accent-gr);
    color: white;
    box-shadow: 0 4px 12px var(--accent-sh);
}
.edu-step.done .edu-step-circle {
    background: var(--accent);
    color: white;
}
.edu-step-label {
    font-size: 9px; font-weight: 700;
    color: #94a3b8; margin-top: 7px;
    text-align: center; text-transform: uppercase;
    letter-spacing: .5px; transition: color .3s;
}
.edu-step.active .edu-step-label,
.edu-step.done  .edu-step-label { color: var(--accent-tx); }

/* ── Stat badges ── */
.stat-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
.stat-box {
    text-align: center; padding: 14px 8px;
    border-radius: 12px; border: 1px solid;
}
.stat-box.green { background: #f0fdf4; border-color: #bbf7d0; }
.stat-box.amber { background: #fffbeb; border-color: #fde68a; }
.stat-box.red   { background: #fff1f2; border-color: #fecdd3; }
.stat-val { font-size: 26px; font-weight: 800; line-height: 1; }
.stat-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-top: 4px; }
.stat-box.green .stat-val { color: #15803d; }
.stat-box.green .stat-lbl { color: #166534; }
.stat-box.amber .stat-val { color: #b45309; }
.stat-box.amber .stat-lbl { color: #92400e; }
.stat-box.red   .stat-val { color: #dc2626; }
.stat-box.red   .stat-lbl { color: #9f1239; }

/* ── Progress bar ── */
.progress-bar-track {
    height: 8px; background: #e2e8f0; border-radius: 99px;
    overflow: hidden; margin-bottom: 6px;
}
.progress-bar-fill {
    height: 100%; border-radius: 99px;
    background: var(--accent-gr);
    transition: width .5s ease;
}

/* ── Validation messages ── */
.val-msg {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 9px 13px; border-radius: 10px;
    margin-bottom: 6px; font-size: 11px; line-height: 1.5;
}
.val-msg-icon {
    width: 20px; height: 20px; border-radius: 50%;
    font-size: 9px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
    color: white;
}

/* ── Table (history) ── */
.edu-table { width: 100%; border-collapse: collapse; }
.edu-table thead tr {
    background: var(--accent-lt);
    border-bottom: 2px solid var(--accent-bd);
}
.edu-table th {
    padding: 10px 14px;
    font-size: 9px; font-weight: 800; color: var(--accent-tx);
    text-transform: uppercase; letter-spacing: .5px;
    text-align: left;
}
.edu-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
}
.edu-table tbody tr:hover { background: var(--accent-ltr); }
.edu-table td { padding: 10px 14px; font-size: 11px; color: #334155; }

/* ── Form inputs ── */
.edu-label {
    display: block;
    font-size: 9px; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 1.5px;
    margin-bottom: 6px;
}
.edu-input {
    width: 100%; height: 42px; padding: 0 14px;
    border-radius: 10px; border: 1.5px solid #e2e8f0;
    background: #f8fafc; font-size: 13px; color: #1e293b;
    outline: none; transition: all .15s;
    box-sizing: border-box;
}
.edu-input:focus { border-color: var(--accent); background: white; }
.edu-select { appearance: none; cursor: pointer; }

/* ── Buttons ── */
.btn-primary {
    height: 44px; padding: 0 20px;
    border-radius: 12px; border: none;
    background: var(--accent-gr);
    color: white; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: opacity .15s;
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    box-shadow: 0 4px 14px var(--accent-sh);
}
.btn-primary:hover { opacity: .88; }
.btn-ghost {
    height: 44px; padding: 0 18px;
    border-radius: 12px; border: 1.5px solid #e2e8f0;
    background: white; color: #475569;
    font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .15s;
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
}
.btn-ghost:hover { border-color: var(--accent-bd); color: var(--accent-tx); background: var(--accent-lt); }

/* ── Format table ── */
.fmt-header {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    background: var(--accent);
}
.fmt-row-a { display: grid; grid-template-columns: repeat(6, 1fr); background: var(--accent-lt); }
.fmt-row-b { display: grid; grid-template-columns: repeat(6, 1fr); }
.fmt-cell {
    padding: 7px 10px;
    font-size: 9px; font-weight: 700;
    border-right: 1px solid rgba(255,255,255,0.12);
}
.fmt-header .fmt-cell { color: rgba(255,255,255,0.85); text-transform: uppercase; letter-spacing: .4px; }
.fmt-row-a  .fmt-cell { color: var(--accent-tx); border-right-color: var(--accent-bd); font-size: 10px; font-weight: 500; }
.fmt-row-b  .fmt-cell { color: #475569; border-right-color: #f1f5f9; border-top: 1px solid #f1f5f9; font-size: 10px; font-weight: 500; }

/* ── Code chip ── */
.code-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 8px;
    background: var(--accent-lt); border: 1px solid var(--accent-bd);
    font-size: 10px;
}
.code-chip-key { font-weight: 800; color: var(--accent-tx); }
.code-chip-val { color: #64748b; }

/* ── Responsive ── */
@media (max-width: 640px) {
    .edu-hero    { padding: 20px 18px; }
    .edu-card-body { padding: 16px; }
    .fmt-header, .fmt-row-a, .fmt-row-b { grid-template-columns: repeat(3, 1fr); }
    .fmt-header .fmt-cell:nth-child(n+4),
    .fmt-row-a  .fmt-cell:nth-child(n+4),
    .fmt-row-b  .fmt-cell:nth-child(n+4) { display: none; }
    .stat-grid { grid-template-columns: 1fr; }
    .edu-tabs .edu-tab { min-width: 80px; padding: 8px 10px; font-size: 11px; }
}
</style>

<div class="edu-wrap">

{{-- ════ FLASH ════ --}}
@if(session('import_success'))
    @php $s = session('import_success'); @endphp
    <div class="flash-success">
        <div class="flash-success-icon">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p style="font-size:13px;font-weight:700;color:var(--accent-tx);margin:0;">Import terminé avec succès !</p>
            <p style="font-size:11px;color:var(--accent-tx);opacity:.8;margin-top:2px;">
                <strong>{{ $s['imported'] }}</strong> importés &nbsp;·&nbsp;
                <strong>{{ $s['skipped'] }}</strong> ignorés (doublons) &nbsp;·&nbsp;
                <strong>{{ $s['errors'] }}</strong> erreurs
            </p>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="flash-success" style="margin-bottom:16px;">
        <div class="flash-success-icon">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;
                border-radius:14px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="font-size:12px;color:#be123c;margin:2px 0;display:flex;align-items:center;gap:6px;">
                <span style="width:16px;height:16px;border-radius:50%;background:#dc2626;color:white;
                             font-size:9px;font-weight:800;display:inline-flex;align-items:center;
                             justify-content:center;flex-shrink:0;">✕</span>
                {{ $e }}
            </p>
        @endforeach
    </div>
@endif

{{-- ════ HERO ════ --}}
<div class="edu-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="edu-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="edu-hero-title">Import EDU — Stagiaires</h1>
            <p class="edu-hero-sub">Importez des stagiaires depuis un fichier Excel ou ajoutez-les manuellement</p>
        </div>
    </div>
    <span class="edu-hero-badge">{{ ucfirst($role) }}</span>
</div>

{{-- ════ TABS ════ --}}
<div class="edu-tabs">
    @can('edu-import')
    <button class="edu-tab active" id="tab-btn-import" onclick="showTab('import')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Import Excel
    </button>
    @endcan

    <button class="edu-tab {{ !auth()->user()->can('edu-import') ? 'active' : '' }}"
            id="tab-btn-history" onclick="showTab('history')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Historique
    </button>

    @can('edu-import')
    <button class="edu-tab" id="tab-btn-manual" onclick="showTab('manual')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Ajout manuel
    </button>
    @endcan
</div>

{{-- ════════════════════════════════════
     TAB: IMPORT EXCEL
════════════════════════════════════ --}}
@can('edu-import')
<div id="tab-import">

    {{-- Steps --}}
    <div class="edu-steps">
        @foreach([['1','Fichier'],['2','Validation'],['3','Confirmation'],['✓','Terminé']] as $i => [$n,$l])
        <div class="edu-step {{ $i===0 ? 'active' : '' }}" id="edu-step-{{ $i+1 }}">
            <div class="edu-step-circle" id="step-circle-{{ $i+1 }}">{{ $n }}</div>
            <div class="edu-step-label" id="step-label-{{ $i+1 }}">{{ $l }}</div>
        </div>
        @endforeach
    </div>

    {{-- STEP 1: Upload --}}
    <div id="step-1">
        {{-- Upload card --}}
        <div class="edu-card">
            <div class="edu-card-head">
                <div>
                    <p class="edu-card-title">Importer un fichier Excel</p>
                    <p class="edu-card-sub">Formats acceptés : .xlsx, .xls, .csv — max 5 Mo</p>
                </div>
                <a href="{{ route('edu-import.template') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                          background:var(--accent-lt);border:1.5px solid var(--accent-bd);
                          border-radius:10px;font-size:11px;font-weight:700;
                          color:var(--accent-tx);text-decoration:none;white-space:nowrap;
                          transition:all .15s;"
                   onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Modèle Excel
                </a>
            </div>
            <div class="edu-card-body">
                <label for="file-input" class="upload-zone" id="upload-zone"
                       ondragover="event.preventDefault();this.classList.add('drag-over')"
                       ondragleave="this.classList.remove('drag-over')"
                       ondrop="handleDrop(event)">
                    <div class="upload-zone-icon">
                        <svg width="28" height="28" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <p id="upload-label"
                       style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:4px;">
                        Glisser le fichier ici ou cliquer pour choisir
                    </p>
                    <p style="font-size:11px;color:#94a3b8;">.xlsx · .xls · .csv</p>
                    <input type="file" id="file-input" accept=".xlsx,.xls,.csv"
                           style="display:none;" onchange="onFileSelected(this)">
                </label>
            </div>
        </div>

        {{-- Format card --}}
        <div class="edu-card">
            <div class="edu-card-head">
                <div>
                    <p class="edu-card-title">Format attendu</p>
                    <p class="edu-card-sub">6 colonnes dans cet ordre exact — la ligne d'en-tête est ignorée</p>
                </div>
            </div>
            <div class="edu-card-body">

                {{-- Excel preview --}}
                <div style="border-radius:12px;overflow:hidden;margin-bottom:18px;border:1px solid var(--accent-bd);">
                    <div class="fmt-header">
                        @foreach(['edu_email','password','nom','prenom','filiere_code','groupe_code'] as $h)
                        <div class="fmt-cell">{{ $h }}</div>
                        @endforeach
                    </div>
                    <div class="fmt-row-a">
                        @foreach(['ahmed@ofppt.ma','pass1234','Ali','Ahmed','DEVDIG','DD-G1A'] as $v)
                        <div class="fmt-cell">{{ $v }}</div>
                        @endforeach
                    </div>
                    <div class="fmt-row-b">
                        @foreach(['sara@ofppt.ma','pass5678','Idrissi','Sara','GI','GI-G1C'] as $v)
                        <div class="fmt-cell">{{ $v }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Codes reference --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;flex-wrap:wrap;">
                    <div>
                        <p style="font-size:9px;font-weight:800;color:var(--accent-tx);
                                  text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                            Codes filières
                        </p>
                        <div style="display:flex;flex-direction:column;gap:5px;">
                            @foreach($filieres as $f)
                            <div class="code-chip">
                                <span class="code-chip-key">{{ $f->code ?? '—' }}</span>
                                <span style="color:#cbd5e1;">·</span>
                                <span class="code-chip-val">{{ $f->name }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p style="font-size:9px;font-weight:800;color:var(--accent-tx);
                                  text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                            Codes groupes
                        </p>
                        <div style="display:flex;flex-direction:column;gap:5px;">
                            @foreach($groupes as $g)
                            <div class="code-chip">
                                <span class="code-chip-key" style="color:#059669;">{{ $g->code ?? '—' }}</span>
                                <span style="color:#cbd5e1;">·</span>
                                <span class="code-chip-val">
                                    {{ $g->filiere->name ?? '' }}
                                    @if($g->name) — {{ $g->name }} @endif
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 2: Validation --}}
    <div id="step-2" style="display:none;">
        <div class="edu-card">
            <div class="edu-card-head">
                <div>
                    <p class="edu-card-title" id="val-title">Validation du fichier</p>
                    <p class="edu-card-sub"  id="val-subtitle"></p>
                </div>
            </div>
            <div class="edu-card-body">

                {{-- Stats --}}
                <div class="stat-grid" style="margin-bottom:16px;">
                    <div class="stat-box green">
                        <div class="stat-val" id="stat-valid">0</div>
                        <div class="stat-lbl">Valides</div>
                    </div>
                    <div class="stat-box amber">
                        <div class="stat-val" id="stat-warn">0</div>
                        <div class="stat-lbl">Avertissements</div>
                    </div>
                    <div class="stat-box red">
                        <div class="stat-val" id="stat-err">0</div>
                        <div class="stat-lbl">Erreurs</div>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" id="val-progress" style="width:0%"></div>
                </div>
                <p id="val-progress-label"
                   style="font-size:10px;color:#64748b;margin-bottom:14px;"></p>

                {{-- Messages --}}
                <div id="val-messages"
                     style="display:flex;flex-direction:column;gap:5px;
                            max-height:220px;overflow-y:auto;margin-bottom:18px;">
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:10px;">
                    <button onclick="goStep(1)" class="btn-ghost" style="flex:1;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Retour
                    </button>
                    <form method="POST" action="{{ route('edu-import.confirm') }}" style="flex:2;">
                        @csrf
                        <button type="submit" id="btn-confirm" class="btn-primary" style="width:100%;">
                            <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirmer l'import
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>{{-- #tab-import --}}
@endcan

{{-- ════════════════════════════════════
     TAB: HISTORY
════════════════════════════════════ --}}
<div id="tab-history" style="display:none;">
    <div class="edu-card">
        <div class="edu-card-head">
            <div>
                <p class="edu-card-title">Historique des imports</p>
                <p class="edu-card-sub">{{ count($history) }} import(s) enregistré(s)</p>
            </div>
        </div>
        @if(count($history) > 0)
        <div style="overflow-x:auto;">
            <table class="edu-table">
                <thead>
                    <tr>
                        @foreach(['Date','Fichier','Importés','Ignorés','Erreurs','Statut','Par'] as $h)
                        <th>{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_reverse($history) as $h)
                    <tr>
                        <td style="white-space:nowrap;">{{ $h['date'] }}</td>
                        <td style="font-weight:600;color:var(--accent);">{{ $h['filename'] }}</td>
                        <td><span style="font-size:13px;font-weight:800;color:#15803d;">{{ $h['imported'] }}</span></td>
                        <td><span style="font-size:13px;font-weight:800;color:#b45309;">{{ $h['skipped'] }}</span></td>
                        <td><span style="font-size:13px;font-weight:800;color:#dc2626;">{{ $h['errors'] }}</span></td>
                        <td>
                            @if($h['errors'] == 0 && $h['skipped'] == 0)
                                <span style="font-size:9px;font-weight:700;padding:3px 10px;border-radius:99px;
                                             background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">
                                    ✓ Succès
                                </span>
                            @else
                                <span style="font-size:9px;font-weight:700;padding:3px 10px;border-radius:99px;
                                             background:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                    ! Partiel
                                </span>
                            @endif
                        </td>
                        <td style="color:#64748b;">{{ $h['user'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="edu-card-body"
             style="padding:48px;text-align:center;">
            <div style="width:56px;height:56px;border-radius:16px;background:var(--accent-lt);
                        display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg width="26" height="26" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:4px;">Aucun import effectué</p>
            <p style="font-size:12px;color:#94a3b8;">L'historique apparaîtra ici après le premier import.</p>
        </div>
        @endif
    </div>
</div>

{{-- ════════════════════════════════════
     TAB: MANUAL
════════════════════════════════════ --}}
@can('edu-import')
<div id="tab-manual" style="display:none;">
    <div class="edu-card">
        <div class="edu-card-head">
            <div>
                <p class="edu-card-title">Ajouter un stagiaire manuellement</p>
                <p class="edu-card-sub">Pour les inscriptions exceptionnelles hors fichier Excel</p>
            </div>
        </div>
        <div class="edu-card-body">
            <form method="POST" action="{{ route('edu-import.manual') }}"
                  style="display:flex;flex-direction:column;gap:16px;">
                @csrf

                {{-- Nom / Prénom --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="edu-label">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom') }}"
                               placeholder="Alami" required class="edu-input">
                    </div>
                    <div>
                        <label class="edu-label">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}"
                               placeholder="Mohammed" required class="edu-input">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="edu-label">Email EDU</label>
                    <input type="email" name="edu_email" value="{{ old('edu_email') }}"
                           placeholder="m.alami@ofppt.ma" required class="edu-input">
                </div>

                {{-- Password --}}
                <div>
                    <label class="edu-label">Mot de passe</label>
                    <input type="password" name="password" required
                           placeholder="Min. 6 caractères" class="edu-input">
                </div>

                {{-- Filière / Groupe --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="edu-label">Filière</label>
                        <select name="filiere_code" id="manual-filiere" required
                                onchange="filterGroups(this.value)"
                                class="edu-input edu-select">
                            <option value="">— Sélectionner —</option>
                            @foreach($filieres as $f)
                                <option value="{{ $f->code }}">{{ $f->code }} — {{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="edu-label">Groupe</label>
                        <select name="groupe_code" id="manual-groupe" required
                                class="edu-input edu-select">
                            <option value="">— Sélectionner la filière d'abord —</option>
                        </select>
                    </div>
                </div>

                {{-- Hint --}}
                <div style="padding:12px 14px;border-radius:10px;background:var(--accent-lt);
                            border:1px solid var(--accent-bd);font-size:11px;color:var(--accent-tx);
                            display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Le stagiaire recevra un email EDU pré-enregistré. Il pourra l'utiliser pour créer son compte.
                </div>

                <div style="display:flex;gap:10px;padding-top:4px;">
                    <button type="reset" class="btn-ghost" style="flex:1;">
                        Réinitialiser
                    </button>
                    <button type="submit" class="btn-primary" style="flex:2;">
                        <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter à la base EDU
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

</div>{{-- .edu-wrap --}}

<script>
// ── Tab switching ────────────────────────────────────────
const TABS = ['import', 'history', 'manual'];
function showTab(name) {
    TABS.forEach(t => {
        const el  = document.getElementById('tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (!el || !btn) return;
        el.style.display  = t === name ? 'block' : 'none';
        btn.classList.toggle('active', t === name);
    });
}

// init: hide history/manual
document.addEventListener('DOMContentLoaded', () => {
    const first = document.querySelector('.edu-tab.active');
    const name  = first ? first.id.replace('tab-btn-','') : 'history';
    showTab(name);
});

// ── Step navigation ──────────────────────────────────────
function goStep(n) {
    [1, 2].forEach(i => {
        const el = document.getElementById('step-' + i);
        if (el) el.style.display = i === n ? 'block' : 'none';
    });
    [1, 2, 3, 4].forEach(i => {
        const circle = document.getElementById('step-circle-' + i);
        const wrap   = document.getElementById('edu-step-'   + i);
        const label  = document.getElementById('step-label-' + i);
        if (!circle || !wrap) return;
        wrap.className = 'edu-step ' + (i < n ? 'done' : i === n ? 'active' : '');
        circle.textContent = i < n ? '✓' : (i === 4 ? '✓' : String(i));
    });
}

// ── File input handler ───────────────────────────────────
function onFileSelected(input) {
    if (input.files && input.files[0]) {
        document.getElementById('upload-label').textContent =
            '📄 ' + input.files[0].name + ' — Validation en cours…';
        uploadAndValidate(input.files[0]);
    }
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('upload-zone').classList.remove('drag-over');
    const file = event.dataTransfer.files[0];
    if (file) {
        document.getElementById('upload-label').textContent =
            '📄 ' + file.name + ' — Validation…';
        uploadAndValidate(file);
    }
}

// ── Upload & validate ────────────────────────────────────
async function uploadAndValidate(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    try {
        const res  = await fetch('{{ route('edu-import.preview') }}', {
            method: 'POST', body: formData,
        });
        const data = await res.json();
        renderValidation(data);
        goStep(2);
    } catch {
        document.getElementById('upload-label').textContent =
            'Erreur — vérifiez le format du fichier.';
    }
}

function renderValidation(data) {
    document.getElementById('val-subtitle').textContent = data.total + ' lignes analysées';
    document.getElementById('stat-valid').textContent   = data.valid;
    document.getElementById('stat-warn').textContent    = data.warn_count;
    document.getElementById('stat-err').textContent     = data.error_count;

    const pct = data.total > 0 ? Math.round((data.valid / data.total) * 100) : 0;
    document.getElementById('val-progress').style.width = pct + '%';
    document.getElementById('val-progress-label').textContent =
        pct + '% des lignes prêtes à importer';

    const box = document.getElementById('val-messages');
    box.innerHTML = '';
    if (data.valid > 0)   box.innerHTML += msgRow('ok',   data.valid + ' stagiaires prêts à être importés.');
    data.warnings.forEach(w => box.innerHTML += msgRow('warn', w));
    data.errors.forEach(e   => box.innerHTML += msgRow('err',  e));

    document.getElementById('btn-confirm').innerHTML =
        `<svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
         </svg>
         Confirmer l'import (${data.valid} lignes)`;
}

function msgRow(type, text) {
    const cfg = {
        ok:   { bg:'#f0fdf4', bd:'#bbf7d0', tx:'#15803d', ic:'✓', icBg:'#22c55e' },
        warn: { bg:'#fffbeb', bd:'#fde68a', tx:'#92400e', ic:'!', icBg:'#f59e0b' },
        err:  { bg:'#fff1f2', bd:'#fecdd3', tx:'#be123c', ic:'✕', icBg:'#ef4444' },
    }[type];
    return `<div class="val-msg" style="background:${cfg.bg};border:1px solid ${cfg.bd};">
        <div class="val-msg-icon" style="background:${cfg.icBg};">${cfg.ic}</div>
        <span style="color:${cfg.tx};">${text}</span>
    </div>`;
}

// ── Filter groupes by filière (manual tab) ────────────────
@php
$groupesJs = $groupes->map(fn($g) => [
    'code'         => $g->code,
    'name'         => $g->name ?? 'G'.$g->id,
    'filiere_code' => $g->filiere?->code,
])->values();
@endphp
const allGroupes = @json($groupesJs);

function filterGroups(filiereCode) {
    const sel = document.getElementById('manual-groupe');
    if (!sel) return;
    sel.innerHTML = '<option value="">— Sélectionner —</option>';
    allGroupes
        .filter(g => g.filiere_code === filiereCode)
        .forEach(g => {
            const o = document.createElement('option');
            o.value = g.code;
            o.textContent = g.code + ' — ' + g.name;
            sel.appendChild(o);
        });
}
</script>

@endsection