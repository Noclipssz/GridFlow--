@props([
    'variant' => 'primary', // primary, secondary, danger, ghost
    'size' => 'md', // sm, md
    'href' => null,
    'type' => 'button',
])
@php
    $base = 'btn';
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => '',
    ];
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
        'ghost' => 'btn-ghost',
    ];
    $classes = trim($base . ' ' . ($sizes[$size] ?? '') . ' ' . ($variants[$variant] ?? $variants['primary']));
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
