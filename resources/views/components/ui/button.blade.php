@props([
  'variant' => 'primary', // primary, secondary, danger, subtle
  'size' => 'md', // sm, md
  'href' => null,
  'type' => 'button',
])
@php
  $base = 'inline-flex items-center justify-center font-medium rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500';
  $sizes = [
    'sm' => 'text-sm px-3 py-1.5',
    'md' => 'text-sm px-4 py-2.5',
  ];
  $variants = [
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700',
    'secondary' => 'bg-slate-800 text-white hover:bg-slate-900',
    'danger' => 'bg-rose-600 text-white hover:bg-rose-700',
    'subtle' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700',
  ];
  $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif

