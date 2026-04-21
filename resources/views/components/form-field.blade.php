{{-- resources/views/components/form-field.blade.php --}}
@props([
    'name',
    'label',
    'type'     => 'text',
    'value'    => '',
    'required' => false,
])

<div>
    <label for="{{ $name }}" class="block text-xs font-semibold text-slate-600 mb-1.5">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <input
        id="{{ $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $required ? 'required' : '' }}
        class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
               focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
               @error($name) border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
    >
    @error($name)
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>