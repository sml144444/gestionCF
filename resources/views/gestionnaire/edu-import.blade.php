{{-- resources/views/gestionnaire/edu-import.blade.php --}}
@extends('layouts.app')
@section('title', 'Import EDU')
@section('page-title', 'Import EDU')

@section('content')
@php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
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
.edu-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.edu-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.edu-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.edu-hero-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.edu-hero-title { font-size:20px; font-weight:800; color:white; margin:0; }
.edu-hero-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }
.edu-hero-badge { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:99px; }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.edu-tabs { display:flex; background:white; border-radius:14px; border:1px solid #e2e8f0; padding:5px; margin-bottom:22px; gap:4px; overflow-x:auto; }
.edu-tab { flex:1; min-width:90px; padding:9px 12px; font-size:12px; font-weight:600; border:none; border-radius:10px; cursor:pointer; transition:all .2s; background:transparent; color:#64748b; display:flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap; }
.edu-tab.active { background:var(--accent-gr); color:white; box-shadow:0 4px 12px var(--accent-sh); }
.edu-tab:not(.active):hover { background:var(--accent-lt); color:var(--accent-tx); }
.edu-tab .tab-badge { font-size:9px; padding:1px 7px; border-radius:99px; font-weight:800; background:rgba(255,255,255,0.18); }
.edu-tab:not(.active) .tab-badge { background:var(--accent-lt); color:var(--accent-tx); }
.edu-card { background:white; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:16px; }
.edu-card-head { padding:16px 22px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.edu-card-body { padding:20px 22px; }
.edu-card-title { font-size:13px; font-weight:800; color:#1e293b; margin:0; }
.edu-card-sub { font-size:11px; color:#64748b; margin-top:2px; }
.upload-zone { border:2px dashed #cbd5e1; border-radius:14px; padding:36px 24px; cursor:pointer; background:#fafcff; display:flex; flex-direction:column; align-items:center; justify-content:center; transition:all .2s; text-align:center; }
.upload-zone:hover,.upload-zone.drag-over { border-color:var(--accent); background:var(--accent-lt); }
.upload-zone-icon { width:60px; height:60px; border-radius:18px; background:var(--accent-lt); border:1px solid var(--accent-bd); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
.edu-steps { display:flex; align-items:center; padding:0 4px; margin-bottom:24px; }
.edu-step { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; }
.edu-step:not(:last-child)::after { content:''; position:absolute; top:16px; left:calc(50% + 18px); right:calc(-50% + 18px); height:2px; background:#e2e8f0; transition:background .3s; }
.edu-step.done::after { background:var(--accent); }
.edu-step-circle { width:32px; height:32px; border-radius:50%; font-size:12px; font-weight:800; display:flex; align-items:center; justify-content:center; z-index:1; transition:all .3s; background:#e2e8f0; color:#94a3b8; }
.edu-step.active .edu-step-circle { background:var(--accent-gr); color:white; box-shadow:0 4px 12px var(--accent-sh); }
.edu-step.done  .edu-step-circle { background:var(--accent); color:white; }
.edu-step-label { font-size:9px; font-weight:700; color:#94a3b8; margin-top:7px; text-align:center; text-transform:uppercase; letter-spacing:.5px; }
.edu-step.active .edu-step-label,.edu-step.done .edu-step-label { color:var(--accent-tx); }
.stat-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }
.stat-box { text-align:center; padding:14px 8px; border-radius:12px; border:1px solid; }
.stat-box.green { background:#f0fdf4; border-color:#bbf7d0; } .stat-box.green .stat-val,.stat-box.green .stat-lbl { color:#15803d; }
.stat-box.amber { background:#fffbeb; border-color:#fde68a; } .stat-box.amber .stat-val,.stat-box.amber .stat-lbl { color:#92400e; }
.stat-box.red   { background:#fff1f2; border-color:#fecdd3; } .stat-box.red   .stat-val,.stat-box.red   .stat-lbl { color:#dc2626; }
.stat-val { font-size:26px; font-weight:800; line-height:1; }
.stat-lbl { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:4px; }
.progress-bar-track { height:8px; background:#e2e8f0; border-radius:99px; overflow:hidden; margin-bottom:6px; }
.progress-bar-fill { height:100%; border-radius:99px; background:var(--accent-gr); transition:width .5s ease; }
.val-msg { display:flex; align-items:flex-start; gap:10px; padding:9px 13px; border-radius:10px; margin-bottom:6px; font-size:11px; line-height:1.5; }
.val-msg-icon { width:20px; height:20px; border-radius:50%; font-size:9px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; color:white; }
.edu-table { width:100%; border-collapse:collapse; }
.edu-table thead tr { background:var(--accent-lt); border-bottom:2px solid var(--accent-bd); }
.edu-table th { padding:10px 14px; font-size:9px; font-weight:800; color:var(--accent-tx); text-transform:uppercase; letter-spacing:.5px; text-align:left; white-space:nowrap; }
.edu-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
.edu-table tbody tr:hover { background:var(--accent-ltr); }
.edu-table td { padding:10px 14px; font-size:11px; color:#334155; vertical-align:middle; }
.edu-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:6px; }
.edu-input { width:100%; height:40px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.edu-input:focus { border-color:var(--accent); background:white; }
.edu-select { appearance:none; cursor:pointer; }
.btn-primary { height:40px; padding:0 18px; border-radius:10px; border:none; background:var(--accent-gr); color:white; font-size:13px; font-weight:700; cursor:pointer; transition:opacity .15s; display:inline-flex; align-items:center; justify-content:center; gap:7px; box-shadow:0 4px 14px var(--accent-sh); }
.btn-primary:hover { opacity:.88; }
.btn-ghost { height:40px; padding:0 16px; border-radius:10px; border:1.5px solid #e2e8f0; background:white; color:#475569; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; justify-content:center; gap:7px; text-decoration:none; }
.btn-ghost:hover { border-color:var(--accent-bd); color:var(--accent-tx); background:var(--accent-lt); }
.btn-action-edit { display:inline-flex; align-items:center; gap:4px; padding:5px 10px; border-radius:8px; border:1.5px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; font-size:10px; font-weight:700; text-decoration:none; transition:all .15s; }
.btn-action-edit:hover { background:#dbeafe; }
.btn-action-delete { display:inline-flex; align-items:center; gap:4px; padding:5px 10px; border-radius:8px; border:1.5px solid #fecdd3; background:#fff1f2; color:#dc2626; font-size:10px; font-weight:700; cursor:pointer; transition:all .15s; }
.btn-action-delete:hover { background:#fee2e2; }
/* ✅ 6-column format table (without promo column) */
.fmt-header { display:grid; grid-template-columns:repeat(6,1fr); background:var(--accent); }
.fmt-row-a  { display:grid; grid-template-columns:repeat(6,1fr); background:var(--accent-lt); }
.fmt-row-b  { display:grid; grid-template-columns:repeat(6,1fr); }
.fmt-cell { padding:7px 10px; font-size:9px; font-weight:700; border-right:1px solid rgba(255,255,255,0.12); }
.fmt-header .fmt-cell { color:rgba(255,255,255,0.85); text-transform:uppercase; }
.fmt-row-a  .fmt-cell,.fmt-row-b .fmt-cell { font-size:10px; font-weight:500; }
.fmt-row-a  .fmt-cell { color:var(--accent-tx); border-right-color:var(--accent-bd); }
.fmt-row-b  .fmt-cell { color:#475569; border-right-color:#f1f5f9; border-top:1px solid #f1f5f9; }
.code-chip { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:8px; background:var(--accent-lt); border:1px solid var(--accent-bd); font-size:10px; }
.code-chip-key { font-weight:800; color:var(--accent-tx); }
.code-chip-val { color:#64748b; }
.imp-avatar { width:30px; height:30px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; color:white; flex-shrink:0; }
.filter-bar { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:16px 18px; margin-bottom:16px; }
.filter-bar-title { font-size:9px; font-weight:800; color:var(--accent-tx); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:12px; display:flex; align-items:center; gap:6px; }
.filter-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:10px; align-items:end; }
.filter-active-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:99px; font-size:10px; font-weight:700; background:#fff7ed; color:#92400e; border:1px solid #fde68a; }
.info-notice { padding:11px 14px; border-radius:10px; font-size:11px; display:flex; align-items:center; gap:8px; }
.info-notice.blue  { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; }
.info-notice.green { background:var(--accent-lt); border:1px solid var(--accent-bd); color:var(--accent-tx); }
</style>

<div class="edu-wrap">

{{-- FLASH MESSAGES --}}
@if(session('import_success'))
    @php $s = session('import_success'); @endphp
    <div class="flash-ok">
        <div class="flash-ok-icon">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p style="font-size:13px;font-weight:700;color:var(--accent-tx);margin:0;">Import terminé !</p>
            <p style="font-size:11px;color:var(--accent-tx);opacity:.8;margin-top:2px;">
                <strong>{{ $s['imported'] }}</strong> importés &nbsp;·&nbsp;
                <strong>{{ $s['skipped'] }}</strong> ignorés &nbsp;·&nbsp;
                <strong>{{ $s['errors'] }}</strong> erreurs
            </p>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="flash-ok">
        <div class="flash-ok-icon">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="flash-ok" style="background:#fff1f2;border-color:#fecdd3;">
        <div class="flash-ok-icon" style="background:#dc2626;">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:#be123c;margin:0;">{{ session('error') }}</p>
    </div>
@endif

@if($errors->any())
    <div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="font-size:12px;color:#be123c;margin:2px 0;">✕ {{ $e }}</p>
        @endforeach
    </div>
@endif

{{-- HERO --}}
<div class="edu-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="edu-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                         M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                         m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="edu-hero-title">Import EDU — Stagiaires</h1>
            <p class="edu-hero-sub">
                <strong style="color:white;">{{ $eduStats['total'] }}</strong> comptes &nbsp;·&nbsp;
                <strong style="color:#86efac;">{{ $eduStats['used'] }}</strong> utilisés &nbsp;·&nbsp;
                <strong style="color:#fde68a;">{{ $eduStats['pending'] }}</strong> en attente
            </p>
        </div>
    </div>
    <span class="edu-hero-badge">{{ ucfirst($role) }}</span>
</div>

{{-- TABS --}}
<div class="edu-tabs">
    @can('edu-import')
    <button class="edu-tab {{ $activeTab==='import' ? 'active' : '' }}" id="tab-btn-import" onclick="showTab('import')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Import Excel
    </button>
    @endcan

    <button class="edu-tab {{ $activeTab==='accounts' ? 'active' : '' }}" id="tab-btn-accounts" onclick="showTab('accounts')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                     M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                     m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Comptes EDU
        <span class="tab-badge">{{ $eduStats['total'] }}</span>
    </button>

    <button class="edu-tab {{ $activeTab==='history' ? 'active' : '' }}" id="tab-btn-history" onclick="showTab('history')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Historique
        <span class="tab-badge">{{ $history->count() }}</span>
    </button>

    @can('edu-import')
    <button class="edu-tab {{ $activeTab==='manual' ? 'active' : '' }}" id="tab-btn-manual" onclick="showTab('manual')">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajout manuel
    </button>
    @endcan
</div>

{{-- TAB : IMPORT EXCEL --}}
@can('edu-import')
<div id="tab-import" style="display:none;">
    <div class="edu-steps">
        @foreach([['1','Fichier'],['2','Validation'],['3','Confirmation'],['✓','Terminé']] as $i => [$n,$l])
        <div class="edu-step {{ $i===0 ? 'active' : '' }}" id="edu-step-{{ $i+1 }}">
            <div class="edu-step-circle" id="step-circle-{{ $i+1 }}">{{ $n }}</div>
            <div class="edu-step-label" id="step-label-{{ $i+1 }}">{{ $l }}</div>
        </div>
        @endforeach
    </div>

    <div id="step-1">
        <div class="edu-card">
            <div class="edu-card-head">
                <div>
                    <p class="edu-card-title">Importer un fichier Excel</p>
                    <p class="edu-card-sub">Formats acceptés : .xlsx, .xls, .csv — max 5 Mo</p>
                </div>
                <a href="{{ route('edu-import.template') }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:var(--accent-lt);border:1.5px solid var(--accent-bd);border-radius:10px;font-size:11px;font-weight:700;color:var(--accent-tx);text-decoration:none;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Modèle Excel
                </a>
            </div>
            <div class="edu-card-body">
                <label for="file-input" class="upload-zone" id="upload-zone" ondragover="event.preventDefault();this.classList.add('drag-over')" ondragleave="this.classList.remove('drag-over')" ondrop="handleDrop(event)">
                    <div class="upload-zone-icon">
                        <svg width="28" height="28" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <p id="upload-label" style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:4px;">Glisser le fichier ici ou cliquer pour choisir</p>
                    <p style="font-size:11px;color:#94a3b8;">.xlsx · .xls · .csv</p>
                    <input type="file" id="file-input" accept=".xlsx,.xls,.csv" style="display:none;" onchange="onFileSelected(this)">
                </label>
            </div>
        </div>

        <div class="edu-card">
            <div class="edu-card-head">
                <div>
                    <p class="edu-card-title">Format attendu</p>
                    {{-- ✅ 6 columns without promo column --}}
                    <p class="edu-card-sub">6 colonnes — le mot de passe est <strong>obligatoire</strong>, la promo est déduite automatiquement du groupe</p>
                </div>
            </div>
            <div class="edu-card-body">
                <div style="border-radius:12px;overflow:hidden;margin-bottom:12px;border:1px solid var(--accent-bd);">
                    <div class="fmt-header">
                        @foreach(['edu_email','nom','prenom','filiere_code','groupe_code','password'] as $h)
                            <div class="fmt-cell">{{ $h }}</div>
                        @endforeach
                    </div>
                    <div class="fmt-row-a">
                        @foreach(['m.alami@ofppt.ma','Alami','Mohammed','DEVDIG','TDEV-101-26','MonPass123!'] as $v)
                            <div class="fmt-cell">{{ $v }}</div>
                        @endforeach
                    </div>
                    <div class="fmt-row-b">
                        @foreach(['s.idrissi@ofppt.ma','Idrissi','Sara','GI','TGI-101-26','Sara2024!'] as $v)
                            <div class="fmt-cell">{{ $v }}</div>
                        @endforeach
                    </div>
                </div>

                <div class="info-notice blue" style="margin-bottom:18px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1 v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Le <strong>password</strong> est obligatoire. La <strong>promo</strong> est déduite
                    automatiquement depuis le code groupe (ex: TDEV-101-26 → promo 2026).
                    Aucun email n'est envoyé automatiquement.
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <p style="font-size:9px;font-weight:800;color:var(--accent-tx);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Codes filières</p>
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
                        <p style="font-size:9px;font-weight:800;color:var(--accent-tx);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Codes groupes</p>
                        <div style="display:flex;flex-direction:column;gap:5px;">
                            @foreach($groupes as $g)
                                <div class="code-chip">
                                    <span class="code-chip-key" style="color:#059669;">{{ $g->code ?? '—' }}</span>
                                    <span style="color:#cbd5e1;">·</span>
                                    <span class="code-chip-val">
                                        {{ $g->filiere->name ?? '' }}@if($g->name) — {{ $g->name }}@endif
                                        @if($g->promo) — promo {{ $g->promo }} @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step-2" style="display:none;">
        <div class="edu-card">
            <div class="edu-card-head">
                <div>
                    <p class="edu-card-title" id="val-title">Validation du fichier</p>
                    <p class="edu-card-sub" id="val-subtitle"></p>
                </div>
            </div>
            <div class="edu-card-body">
                <div class="stat-grid" style="margin-bottom:16px;">
                    <div class="stat-box green"><div class="stat-val" id="stat-valid">0</div><div class="stat-lbl">Valides</div></div>
                    <div class="stat-box amber"><div class="stat-val" id="stat-warn">0</div><div class="stat-lbl">Avertissements</div></div>
                    <div class="stat-box red"><div class="stat-val" id="stat-err">0</div><div class="stat-lbl">Erreurs</div></div>
                </div>
                <div class="progress-bar-track"><div class="progress-bar-fill" id="val-progress" style="width:0%"></div></div>
                <p id="val-progress-label" style="font-size:10px;color:#64748b;margin-bottom:14px;"></p>
                <div id="val-messages" style="display:flex;flex-direction:column;gap:5px;max-height:220px;overflow-y:auto;margin-bottom:18px;"></div>
                <div style="display:flex;gap:10px;">
                    <button onclick="goStep(1)" class="btn-ghost" style="flex:1;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Retour
                    </button>
                    <form method="POST" action="{{ route('edu-import.confirm') }}" style="flex:2;">
                        @csrf
                        <button type="submit" id="btn-confirm" class="btn-primary" style="width:100%;height:44px;">
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
</div>
@endcan

{{-- TAB : COMPTES EDU --}}
<div id="tab-accounts" style="display:none;">
    <div class="filter-bar">
        <div class="filter-bar-title">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894 l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filtrer les comptes EDU
            @if($hasFilters)
                <span class="filter-active-badge">Filtres actifs</span>
            @endif
        </div>

        <form method="GET" action="{{ route('edu-import.index') }}" id="filter-form">
            <input type="hidden" name="tab" value="accounts">
            <div class="filter-grid">
                <div style="grid-column:span 2;">
                    <label class="edu-label">Recherche</label>
                    <div style="position:relative;">
                        <svg width="14" height="14" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $filterSearch }}" placeholder="Nom, prénom ou email…" class="edu-input" style="padding-left:34px;">
                    </div>
                </div>
                <div>
                    <label class="edu-label">Filière</label>
                    <select name="filiere_code" id="filter-filiere" class="edu-input edu-select" onchange="updateGroupeOptions(this.value)">
                        <option value="">Toutes</option>
                        @foreach($eduFiliereCodes as $fc)
                            <option value="{{ $fc }}" {{ $filterFiliere===$fc ? 'selected':'' }}>{{ $fc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="edu-label">Groupe</label>
                    <select name="groupe_code" id="filter-groupe" class="edu-input edu-select">
                        <option value="">Tous</option>
                        @foreach($eduGroupeCodes as $gc)
                            <option value="{{ $gc }}" {{ $filterGroupe===$gc ? 'selected':'' }}>{{ $gc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="edu-label">Année scolaire</label>
                    <select name="annee_scolaire" class="edu-input edu-select">
                        <option value="">Toutes</option>
                        @foreach($anneesScolaires as $annee)
                            <option value="{{ $annee }}" {{ $filterAnnee===$annee ? 'selected':'' }}>{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="edu-label">Statut</label>
                    <select name="statut" class="edu-input edu-select">
                        <option value="">Tous</option>
                        <option value="used" {{ $filterStatut==='used' ? 'selected':'' }}>✓ Compte créé</option>
                        <option value="pending" {{ $filterStatut==='pending' ? 'selected':'' }}>⏳ En attente</option>
                    </select>
                </div>
                <div>
                    <label class="edu-label">Date début</label>
                    <input type="date" name="date_from" value="{{ $filterDateFrom }}" class="edu-input">
                </div>
                <div>
                    <label class="edu-label">Date fin</label>
                    <input type="date" name="date_to" value="{{ $filterDateTo }}" class="edu-input">
                </div>
                <div style="display:flex;gap:8px;align-items:flex-end;">
                    <button type="submit" class="btn-primary" style="flex:1;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                    @if($hasFilters)
                    <a href="{{ route('edu-import.index', ['tab'=>'accounts']) }}" class="btn-ghost" style="flex:1;text-align:center;">✕ Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="edu-card">
        <div class="edu-card-head">
            <div>
                <p class="edu-card-title">Comptes EDU importés</p>
                <p class="edu-card-sub">
                    @if($hasFilters)
                        <strong style="color:var(--accent);">{{ $eduAccounts->total() }}</strong> résultat(s) filtrés sur {{ $eduStats['total'] }} total
                    @else
                        <span style="color:#15803d;font-weight:700;">{{ $eduStats['used'] }} utilisés</span>
                        &nbsp;·&nbsp;
                        <span style="color:#b45309;font-weight:700;">{{ $eduStats['pending'] }} en attente</span>
                        &nbsp;·&nbsp; {{ $eduStats['total'] }} total
                    @endif
                </p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span style="padding:4px 12px;border-radius:99px;font-size:10px;font-weight:700;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">✓ {{ $eduStats['used'] }} utilisés</span>
                <span style="padding:4px 12px;border-radius:99px;font-size:10px;font-weight:700;background:#fffbeb;color:#92400e;border:1px solid #fde68a;">⏳ {{ $eduStats['pending'] }} en attente</span>
            </div>
        </div>

        @if($eduAccounts->isEmpty())
            <div class="edu-card-body" style="padding:48px;text-align:center;">
                <div style="width:56px;height:56px;border-radius:16px;background:var(--accent-lt);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <svg width="26" height="26" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857 M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857 m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">{{ $hasFilters ? 'Aucun résultat pour ces filtres' : 'Aucun compte EDU' }}</p>
                <p style="font-size:12px;color:#94a3b8;margin:0;">{{ $hasFilters ? 'Essayez de modifier ou réinitialiser les filtres.' : 'Importez des stagiaires pour les voir ici.' }}</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="edu-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom complet</th>
                            <th>Email EDU</th>
                            <th>Filière</th>
                            <th>Groupe</th>
                            <th>Statut</th>
                            @can('edu-import')
                            <th>Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eduAccounts as $edu)
                        @php
                            $initials = strtoupper(substr($edu->prenom ?? '?', 0, 1)) . strtoupper(substr($edu->nom ?? '?', 0, 1));
                        @endphp
                        <tr>
                            <td style="color:#94a3b8;font-size:10px;">{{ $edu->id }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:30px;height:30px;border-radius:8px;flex-shrink:0;background:var(--accent-lt);border:1px solid var(--accent-bd);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--accent-tx);">{{ $initials }}</div>
                                    <div style="font-weight:700;color:#0f172a;font-size:12px;">{{ $edu->prenom ?? '' }} {{ $edu->nom ?? '' }}</div>
                                </div>
                             </td>
                            <td style="color:#1e40af;font-weight:600;">{{ $edu->edu_email }}</td>
                            <td>
                                @if($edu->filiere_code)
                                    <span style="padding:2px 9px;border-radius:6px;font-size:10px;font-weight:700;background:var(--accent-lt);color:var(--accent-tx);">{{ $edu->filiere_code }}</span>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                             </td>
                            <td>
                                @if($edu->groupe_code)
                                    <span style="padding:2px 9px;border-radius:6px;font-size:10px;font-weight:700;background:#f1f5f9;color:#334155;">{{ $edu->groupe_code }}</span>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                             </td>
                            <td>
                                @if($edu->used)
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:9px;font-weight:700;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;"><span style="width:5px;height:5px;border-radius:50%;background:#22c55e;display:inline-block;"></span> Compte créé</span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:9px;font-weight:700;background:#fffbeb;color:#92400e;border:1px solid #fde68a;"><span style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;"></span> En attente</span>
                                @endif
                             </td>
                            @can('edu-import')
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <a href="{{ route('edu-import.edit', $edu->id) }}" class="btn-action-edit">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Modifier
                                    </a>
                                    <form method="POST" action="{{ route('edu-import.destroy', $edu->id) }}" onsubmit="return confirm('Supprimer {{ $edu->prenom }} {{ $edu->nom }} ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-delete">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858 L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                             </td>
                            @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($eduAccounts->hasPages())
                <div style="padding:14px 22px;border-top:1px solid #f1f5f9;">{{ $eduAccounts->links() }}</div>
            @endif
        @endif
    </div>
</div>

{{-- TAB : HISTORIQUE --}}
<div id="tab-history" style="display:none;">
    <div class="edu-card">
        <div class="edu-card-head">
            <div>
                <p class="edu-card-title">Historique des imports</p>
                <p class="edu-card-sub">{{ $history->count() }} opération(s) — cliquez sur une ligne pour voir les détails</p>
            </div>
        </div>

        @if($history->isEmpty())
            <div class="edu-card-body" style="padding:48px;text-align:center;">
                <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucun import effectué</p>
                <p style="font-size:12px;color:#94a3b8;margin:0;">L'historique apparaîtra ici après le premier import.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="edu-table">
                    <thead>
                        <tr>
                            <th>Date & Heure</th>
                            <th>Importé par</th>
                            <th>Fichier / Source</th>
                            <th>Importés</th>
                            <th>Ignorés</th>
                            <th>Erreurs</th>
                            <th>Statut</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $log)
                        @php
                            $importerRole = $log->user?->role ?? 'admin';
                            $avatarBg = ['admin'=>'#0a6640','gestionnaire'=>'#1e293b','formateur'=>'#1a4f8a'][$importerRole] ?? '#475569';
                            $name = $log->user?->name ?? 'Inconnu';
                            $importerInitials = strtoupper(substr($name, 0, 1)) . strtoupper(substr(explode(' ', $name . ' ')[1] ?? '', 0, 1));
                        @endphp
                        <tr style="cursor:pointer;" onclick="openLogModal({{ $log->id }})">
                            <td style="white-space:nowrap;">
                                <div style="font-weight:600;color:#334155;">{{ $log->created_at->format('d M Y') }}</div>
                                <div style="font-size:10px;color:#94a3b8;">{{ $log->created_at->format('H:i') }}</div>
                             </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="imp-avatar" style="background:{{ $avatarBg }};">{{ $importerInitials }}</div>
                                    <div>
                                        <div style="font-weight:700;color:#0f172a;font-size:12px;">{{ $name }}</div>
                                        <div style="font-size:9px;text-transform:capitalize;color:{{ $avatarBg }};font-weight:600;">{{ $importerRole }}</div>
                                    </div>
                                </div>
                             </td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-weight:600;color:var(--accent);font-size:11px;">
                                    @if($log->filename === 'Ajout manuel')
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    @else
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293 l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                    {{ $log->filename }}
                                </span>
                             </td>
                            <td><span style="font-size:14px;font-weight:800;color:#15803d;">{{ $log->imported }}</span></td>
                            <td><span style="font-size:14px;font-weight:800;color:#b45309;">{{ $log->skipped }}</span></td>
                            <td><span style="font-size:14px;font-weight:800;color:#dc2626;">{{ $log->errors }}</span></td>
                            <td>
                                @if($log->errors == 0 && $log->skipped == 0)
                                    <span style="font-size:9px;font-weight:700;padding:3px 10px;border-radius:99px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">✓ Succès</span>
                                @elseif($log->errors == 0)
                                    <span style="font-size:9px;font-weight:700;padding:3px 10px;border-radius:99px;background:#fffbeb;color:#92400e;border:1px solid #fde68a;">! Partiel</span>
                                @else
                                    <span style="font-size:9px;font-weight:700;padding:3px 10px;border-radius:99px;background:#fff1f2;color:#dc2626;border:1px solid #fecdd3;">✕ Erreurs</span>
                                @endif
                             </td>
                            <td onclick="event.stopPropagation()">
                                <button onclick="openLogModal({{ $log->id }})" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;border:1.5px solid var(--accent-bd);background:var(--accent-lt);color:var(--accent-tx);font-size:10px;font-weight:700;cursor:pointer;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Voir détails
                                </button>
                             </td>
                         </>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- MODAL DÉTAILS LOG --}}
<div id="log-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:white;border-radius:20px;width:100%;max-width:860px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.25);">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <p id="modal-title" style="font-size:15px;font-weight:800;color:#1e293b;margin:0;">Détails de l'import</p>
                <p id="modal-subtitle" style="font-size:11px;color:#64748b;margin-top:3px;"></p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <button id="modal-export-btn" onclick="exportModalToCSV()" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:var(--accent-lt);border:1.5px solid var(--accent-bd);border-radius:10px;font-size:11px;font-weight:700;color:var(--accent-tx);cursor:pointer;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exporter CSV
                </button>
                <button onclick="closeLogModal()" style="width:34px;height:34px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;font-size:16px;cursor:pointer;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
            </div>
        </div>
        <div id="modal-stats" style="padding:14px 24px;border-bottom:1px solid #f1f5f9;display:flex;gap:12px;flex-wrap:wrap;flex-shrink:0;"></div>
        <div style="overflow-y:auto;flex:1;padding:0;">
            <div id="modal-loading" style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">Chargement…</div>
            <div id="modal-table-wrap" style="display:none;">
                <table class="edu-table" style="width:100%;">
                    <thead><tr><th>#</th><th>Nom complet</th><th>Email EDU</th><th>Filière</th><th>Groupe</th><th>Statut</th></thead>
                    <tbody id="modal-tbody"></tbody>
                </table>
                <div id="modal-empty" style="display:none;padding:48px;text-align:center;color:#94a3b8;font-size:13px;">Aucun compte lié à cet import<p style="font-size:11px;margin-top:6px;">(Les imports effectués avant cette mise à jour n'ont pas de liaison enregistrée)</p></div>
            </div>
        </div>
    </div>
</div>

{{-- TAB : AJOUT MANUEL --}}
@can('edu-import')
<div id="tab-manual" style="display:none;">
    <div class="edu-card">
        <div class="edu-card-head">
            <div>
                <p class="edu-card-title">Ajouter un stagiaire manuellement</p>
                <p class="edu-card-sub">Pour les inscriptions exceptionnelles hors fichier Excel (promo déduite automatiquement du groupe)</p>
            </div>
        </div>
        <div class="edu-card-body">
            <form method="POST" action="{{ route('edu-import.manual') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div><label class="edu-label">Nom</label><input type="text" name="nom" value="{{ old('nom') }}" placeholder="Alami" required class="edu-input"></div>
                    <div><label class="edu-label">Prénom</label><input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Mohammed" required class="edu-input"></div>
                </div>
                <div><label class="edu-label">Email EDU</label><input type="email" name="edu_email" value="{{ old('edu_email') }}" placeholder="m.alami@ofppt.ma" required class="edu-input"></div>
                <div>
                    <label class="edu-label">Mot de passe</label>
                    <input type="password" name="password" value="{{ old('password') }}" placeholder="Min. 6 caractères" required class="edu-input">
                    @error('password')<p style="font-size:11px;color:#dc2626;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div class="info-notice blue">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1 v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Le mot de passe est obligatoire. La promo est déduite automatiquement du groupe sélectionné. Aucun email n'est envoyé automatiquement.</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="edu-label">Filière</label>
                        <select name="filiere_code" id="manual-filiere" required onchange="filterGroups(this.value)" class="edu-input edu-select">
                            <option value="">— Sélectionner —</option>
                            @foreach($filieres as $f)
                                <option value="{{ $f->code }}" {{ old('filiere_code') === $f->code ? 'selected' : '' }}>{{ $f->code }} — {{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="edu-label">Année de formation</label>
                        <div style="display:flex;gap:6px;height:40px;align-items:center;">
                            @foreach([1 => '1ère', 2 => '2ème', 3 => '3ème'] as $val => $label)
                            <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:6px;border-radius:9px;cursor:pointer;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:11px;font-weight:700;color:#64748b;transition:all .15s;" id="annee-lbl-{{ $val }}" onclick="selectAnnee({{ $val }})">
                                <input type="radio" name="_annee_filter" value="{{ $val }}" id="annee-radio-{{ $val }}" style="display:none;" {{ old('_annee_filter', 1) == $val ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div>
                    <label class="edu-label">Groupe</label>
                    <select name="groupe_code" id="manual-groupe" required class="edu-input edu-select">
                        <option value="">— Sélectionner filière et année —</option>
                    </select>
                </div>
                <div class="info-notice green">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Le stagiaire utilisera cet email et ce mot de passe pour créer son compte. La promo du groupe sera automatiquement appliquée.
                </div>
                <div style="display:flex;gap:10px;padding-top:4px;">
                    <button type="reset" class="btn-ghost" style="flex:1;">Réinitialiser</button>
                    <button type="submit" class="btn-primary" style="flex:2;height:44px;">
                        <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Ajouter à la base EDU
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

</div>

<script>
const TABS = ['import','accounts','history','manual'];
function showTab(name) {
    TABS.forEach(t => {
        const el = document.getElementById('tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (el) el.style.display = t === name ? 'block' : 'none';
        if (btn) btn.classList.toggle('active', t === name);
    });
}
document.addEventListener('DOMContentLoaded', () => showTab('{{ $activeTab }}'));

function goStep(n) {
    [1,2].forEach(i => { const el = document.getElementById('step-'+i); if(el) el.style.display = i===n ? 'block' : 'none'; });
    [1,2,3,4].forEach(i => {
        const c = document.getElementById('step-circle-'+i);
        const w = document.getElementById('edu-step-'+i);
        if(!c||!w) return;
        w.className = 'edu-step ' + (i < n ? 'done' : i===n ? 'active' : '');
        c.textContent = i < n ? '✓' : (i===4 ? '✓' : String(i));
    });
}

function onFileSelected(input) {
    if(input.files && input.files[0]) {
        document.getElementById('upload-label').textContent = '📄 ' + input.files[0].name + ' — Validation…';
        uploadAndValidate(input.files[0]);
    }
}
function handleDrop(event) {
    event.preventDefault();
    document.getElementById('upload-zone').classList.remove('drag-over');
    const file = event.dataTransfer.files[0];
    if(file) { document.getElementById('upload-label').textContent = '📄 ' + file.name; uploadAndValidate(file); }
}
async function uploadAndValidate(file) {
    const fd = new FormData();
    fd.append('file', file);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
    try {
        const res = await fetch('{{ route('edu-import.preview') }}', { method:'POST', body:fd });
        const data = await res.json();
        renderValidation(data);
        goStep(2);
    } catch { document.getElementById('upload-label').textContent = 'Erreur — vérifiez le format.'; }
}
function renderValidation(data) {
    document.getElementById('val-subtitle').textContent = data.total + ' lignes analysées';
    document.getElementById('stat-valid').textContent = data.valid;
    document.getElementById('stat-warn').textContent = data.warn_count;
    document.getElementById('stat-err').textContent = data.error_count;
    const pct = data.total > 0 ? Math.round((data.valid / data.total) * 100) : 0;
    document.getElementById('val-progress').style.width = pct + '%';
    document.getElementById('val-progress-label').textContent = pct + '% des lignes prêtes';
    const box = document.getElementById('val-messages');
    box.innerHTML = '';
    if(data.valid > 0) box.innerHTML += msgRow('ok', data.valid + ' stagiaire(s) prêt(s) à importer.');
    data.warnings.forEach(w => box.innerHTML += msgRow('warn', w));
    data.errors.forEach(e => box.innerHTML += msgRow('err', e));
    document.getElementById('btn-confirm').innerHTML = `<svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Confirmer l'import (${data.valid} lignes)`;
    document.getElementById('btn-confirm').disabled = data.valid === 0;
    document.getElementById('btn-confirm').style.opacity = data.valid === 0 ? '.5' : '1';
}
function msgRow(type, text) {
    const c = { ok:{ bg:'#f0fdf4', bd:'#bbf7d0', tx:'#15803d', ic:'✓', icBg:'#22c55e' }, warn:{ bg:'#fffbeb', bd:'#fde68a', tx:'#92400e', ic:'!', icBg:'#f59e0b' }, err:{ bg:'#fff1f2', bd:'#fecdd3', tx:'#be123c', ic:'✕', icBg:'#ef4444' } }[type];
    return `<div class="val-msg" style="background:${c.bg};border:1px solid ${c.bd};"><div class="val-msg-icon" style="background:${c.icBg};">${c.ic}</div><span style="color:${c.tx};">${text}</span></div>`;
}

@php
$allEduGroupes = \App\Models\Edu::select('filiere_code','groupe_code')->distinct()->orderBy('groupe_code')->get()->groupBy('filiere_code');
@endphp
const eduGroupesByFiliere = @json($allEduGroupes->map(fn($g) => $g->pluck('groupe_code')));

function updateGroupeOptions(filiereCode) {
    const sel = document.getElementById('filter-groupe');
    if(!sel) return;
    sel.innerHTML = '<option value="">Tous</option>';
    const codes = filiereCode ? (eduGroupesByFiliere[filiereCode] || []) : Object.values(eduGroupesByFiliere).flat();
    codes.forEach(gc => { const o = document.createElement('option'); o.value = gc; o.textContent = gc; sel.appendChild(o); });
}

@php
$groupesJs = $groupes->map(fn($g) => [
    'code'         => $g->code,
    'name'         => $g->name ?? 'G'.$g->id,
    'filiere_code' => $g->filiere?->code,
    'annee'        => $g->annee ?? 1,
    'promo'        => $g->promo,
])->values();
@endphp
const allGroupes = @json($groupesJs);
let selectedAnnee = {{ old('_annee_filter', 1) }};

function selectAnnee(val) {
    selectedAnnee = val;
    [1,2,3].forEach(v => {
        const lbl = document.getElementById('annee-lbl-'+v);
        const radio = document.getElementById('annee-radio-'+v);
        if(!lbl) return;
        const isActive = v === val;
        lbl.style.background = isActive ? 'var(--accent-lt)' : '#f8fafc';
        lbl.style.borderColor = isActive ? 'var(--accent)' : '#e2e8f0';
        lbl.style.color = isActive ? 'var(--accent-tx)' : '#64748b';
        if(radio) radio.checked = isActive;
    });
    const fc = document.getElementById('manual-filiere')?.value;
    filterGroups(fc);
}
function filterGroups(fc) {
    const sel = document.getElementById('manual-groupe');
    if(!sel) return;
    const anneeLabels = {1:'An.1',2:'An.2',3:'An.3'};
    
    sel.innerHTML = '<option value="">— Sélectionner —</option>';
    
    allGroupes
        .filter(g => (!fc || g.filiere_code === fc) && g.annee === selectedAnnee)
        .forEach(g => {
            const o = document.createElement('option');
            o.value = g.code;
            o.textContent = g.code + ' — ' + g.name + ' (' + anneeLabels[g.annee] + (g.promo ? ' - promo ' + g.promo : '') + ')';
            sel.appendChild(o);
        });
    
    if(sel.options.length === 1) { 
        const o = document.createElement('option'); 
        o.disabled = true; 
        o.textContent = '— Aucun groupe pour ces critères —'; 
        sel.appendChild(o); 
    }
}

document.addEventListener('DOMContentLoaded', function() {
    selectAnnee(selectedAnnee);
    const filiereSelect = document.getElementById('filter-filiere');
    if(filiereSelect && filiereSelect.value) updateGroupeOptions(filiereSelect.value);
});

let modalData = [];
function openLogModal(logId) {
    const overlay = document.getElementById('log-modal-overlay');
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('modal-loading').style.display = 'block';
    document.getElementById('modal-table-wrap').style.display = 'none';
    document.getElementById('modal-stats').innerHTML = '';
    document.getElementById('modal-tbody').innerHTML = '';
    modalData = [];
    fetch(`/edu-import/log/${logId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
        const log = data.log;
        modalData = data.accounts;
        document.getElementById('modal-title').textContent = log.filename === 'Ajout manuel' ? 'Ajout manuel — détails' : `Import : ${log.filename}`;
        document.getElementById('modal-subtitle').textContent = `${log.created_at} · par ${log.user}`;
        document.getElementById('modal-stats').innerHTML = `<div style="display:flex;gap:10px;flex-wrap:wrap;"><span style="padding:5px 14px;border-radius:99px;font-size:11px;font-weight:700;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">✓ ${log.imported} importés</span><span style="padding:5px 14px;border-radius:99px;font-size:11px;font-weight:700;background:#fffbeb;color:#92400e;border:1px solid #fde68a;">⏭ ${log.skipped} ignorés</span><span style="padding:5px 14px;border-radius:99px;font-size:11px;font-weight:700;background:#fff1f2;color:#dc2626;border:1px solid #fecdd3;">✕ ${log.errors} erreurs</span></div>`;
        document.getElementById('modal-loading').style.display = 'none';
        document.getElementById('modal-table-wrap').style.display = 'block';
        if(modalData.length === 0) { document.getElementById('modal-empty').style.display = 'block'; return; }
        const tbody = document.getElementById('modal-tbody');
        tbody.innerHTML = modalData.map((acc,i) => {
            const initials = (acc.prenom?.[0] ?? '?').toUpperCase() + (acc.nom?.[0] ?? '?').toUpperCase();
            const statusBadge = acc.used ? '<span style="padding:3px 10px;border-radius:99px;font-size:9px;font-weight:700;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">✓ Compte créé</span>' : '<span style="padding:3px 10px;border-radius:99px;font-size:9px;font-weight:700;background:#fffbeb;color:#92400e;border:1px solid #fde68a;">⏳ En attente</span>';
            return `<tr><td style="color:#94a3b8;font-size:10px;">${acc.id}</td><td><div style="display:flex;align-items:center;gap:8px;"><div style="width:28px;height:28px;border-radius:8px;flex-shrink:0;background:var(--accent-lt);border:1px solid var(--accent-bd);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--accent-tx);">${initials}</div><span style="font-weight:700;color:#0f172a;font-size:12px;">${acc.prenom ?? ''} ${acc.nom ?? ''}</span></div></td><td style="color:#1e40af;font-weight:600;font-size:11px;">${acc.edu_email}</td><td><span style="padding:2px 9px;border-radius:6px;font-size:10px;font-weight:700;background:var(--accent-lt);color:var(--accent-tx);">${acc.filiere_code ?? '—'}</span></td><td><span style="padding:2px 9px;border-radius:6px;font-size:10px;font-weight:700;background:#f1f5f9;color:#334155;">${acc.groupe_code ?? '—'}</span></td><td>${statusBadge}</td></tr>`;
        }).join('');
    }).catch(() => { document.getElementById('modal-loading').textContent = 'Erreur de chargement.'; });
}
function closeLogModal() {
    document.getElementById('log-modal-overlay').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('log-modal-overlay').addEventListener('click', function(e) { if(e.target === this) closeLogModal(); });
function exportModalToCSV() {
    if(!modalData.length) return;
    const rows = [['ID','Prénom','Nom','Email EDU','Filière','Groupe','Statut']];
    modalData.forEach(a => { rows.push([a.id, a.prenom ?? '', a.nom ?? '', a.edu_email, a.filiere_code ?? '', a.groupe_code ?? '', a.used ? 'Compte créé' : 'En attente']); });
    const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type:'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'details_import.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection