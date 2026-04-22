{{--
    Reusable input partial.
    Variables: $label, $name, $type, $value (opt), $placeholder (opt), $required (opt bool)
    Special: $type='tel' → auto-adds phone validation (pattern + oninput filter + inputmode)
--}}
@php
    $val   = $value       ?? old($name, '');
    $ph    = $placeholder ?? ($type === 'tel' ? '+212 6XX XXX XXX' : '');
    $req   = $required    ?? false;
    $hasErr = $errors->has($name);
    $borderColor = $hasErr ? '#fca5a5' : '#e2e8f0';
    $bgColor     = $hasErr ? '#fef2f2' : '#f8fafc';
    $isPhone = $type === 'tel';
@endphp
<div>
    <label for="{{ $name }}"
           style="display:block; font-size:11px; font-weight:700; color:#475569; margin-bottom:5px;">
        {{ $label }}@if($req)<span style="color:#ef4444; margin-left:2px;">*</span>@endif
    </label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $type !== 'password' ? $val : '' }}"
        placeholder="{{ $ph }}"
        {{ $req ? 'required' : '' }}
        @if($isPhone)
            inputmode="tel"
            pattern="[\+0-9\s\-\(\)\.]{6,20}"
            title="Numéro de téléphone valide uniquement (ex: +212 6XX XXX XXX)"
            maxlength="20"
            oninput="this.value=this.value.replace(/[^+0-9\s\-\(\)\.]/g,'')"
        @endif
        style="width:100%; height:40px; padding:0 12px; border-radius:10px; box-sizing:border-box;
               border:1.5px solid {{ $borderColor }}; background:{{ $bgColor }};
               font-size:13px; color:#1e293b; outline:none;"
        onfocus="this.style.borderColor='#0a6640'; this.style.background='white';"
        onblur="this.style.borderColor='{{ $borderColor }}'; this.style.background='{{ $bgColor }}';">
    @if($isPhone)
        <div style="font-size:9px; color:#94a3b8; margin-top:3px;">
            Chiffres, espaces, +, -, ( ) uniquement
        </div>
    @endif
    @error($name)
        <div style="font-size:10px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
    @enderror
</div>