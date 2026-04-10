{{-- resources/views/components/nav-item.blade.php --}}
@props(['route' => '#', 'icon', 'label', 'active' => false])

<a href="{{ $route }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group
          {{ $active || request()->routeIs(str_replace('/', '.', ltrim($route, '/')))
             ? 'bg-white/20 text-white'
             : 'text-white/65 hover:bg-white/10 hover:text-white' }}">

    {{-- Icon --}}
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
    </svg>

    {{-- Label (hidden when collapsed) --}}
    <span class="text-sm whitespace-nowrap overflow-hidden transition-all duration-300 leading-none"
          x-bind:class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
        {{ $label }}
    </span>
</a>