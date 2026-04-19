{{--
    Reusable input partial.
    Variables: $label, $name, $type, $value (opt), $placeholder (opt), $required (opt bool)
--}}
@php
    $val   = $value       ?? old($name, '');
    $ph    = $placeholder ?? '';
    $req   = $required    ?? false;
    $hasErr = $errors->has($name);
    $borderColor = $hasErr ? '#fca5a5' : '#e2e8f0';
    $bgColor     = $hasErr ? '#fef2f2' : '#f8fafc';
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
        style="width:100%; height:40px; padding:0 12px; border-radius:10px; box-sizing:border-box;
               border:1.5px solid {{ $borderColor }}; background:{{ $bgColor }};
               font-size:13px; color:#1e293b; outline:none;"
        onfocus="this.style.borderColor='#0a6640'; this.style.background='white';"
        onblur="this.style.borderColor='{{ $borderColor }}'; this.style.background='{{ $bgColor }}';">
    @error($name)
        <div style="font-size:10px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
    @enderror
</div>