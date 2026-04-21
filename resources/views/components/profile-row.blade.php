{{-- resources/views/components/profile-row.blade.php --}}
@props(['label', 'value'])
 
<div class="flex items-start justify-between gap-3 py-2 border-b border-slate-50 last:border-0">
    <span class="text-xs font-semibold text-slate-400 whitespace-nowrap flex-shrink-0">{{ $label }}</span>
    <span class="text-xs text-slate-700 font-medium text-right break-all">{{ $value }}</span>
</div>
 