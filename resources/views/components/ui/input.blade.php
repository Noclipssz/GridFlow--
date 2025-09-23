@props(['type' => 'text'])
@php
  $hasDynamicType = $attributes->has('x-bind:type');
  $classes = 'w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100';
@endphp
<input @unless($hasDynamicType) type="{{ $type }}" @endunless {{ $attributes->merge(['class' => $classes]) }}>
