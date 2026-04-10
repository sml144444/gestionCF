{{-- resources/views/components/nav-section.blade.php --}}
@props(['label'])

<div class="px-3 pt-4 pb-1 overflow-hidden transition-all duration-300"
     x-bind:class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 py-0'">
    <p class="text-[9px] font-bold uppercase tracking-widest text-white/35">{{ $label }}</p>
</div>