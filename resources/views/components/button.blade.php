@props(['variant' => 'primary', 'icon' => null, 'iconRight' => false])

@php
    $base = 'inline-flex items-center px-3 py-2 border rounded-md font-medium text-sm focus:outline-none transition';

    $variants = [
        'primary' => 'bg-indigo-600 text-white border-transparent hover:bg-indigo-700',
        'secondary' => 'bg-white text-gray-800 border border-gray-200 hover:bg-gray-50',
        'success' => 'bg-green-600 text-white border-transparent hover:bg-green-700',
        'danger' => 'bg-red-600 text-white border-transparent hover:bg-red-700',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon && ! $iconRight)
        <x-icon name="{{ $icon }}" class="h-4 w-4 mr-2" />
    @endif

    {{ $slot }}

    @if($icon && $iconRight)
        <x-icon name="{{ $icon }}" class="h-4 w-4 ml-2" />
    @endif
</button>
