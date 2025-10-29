@props(['color' => 'gray'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-800',
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
    ];
    $class = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' . ($colors[$color] ?? $colors['gray']);
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</span>
