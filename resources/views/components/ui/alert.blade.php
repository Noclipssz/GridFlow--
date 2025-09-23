@props(['type' => 'info'])
@php
  $map = [
    'info' => 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-900/50 dark:bg-sky-900/30 dark:text-sky-100',
    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-900/30 dark:text-emerald-100',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/50 dark:bg-amber-900/30 dark:text-amber-100',
    'danger' => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/50 dark:bg-rose-900/30 dark:text-rose-100',
  ];
  $class = $map[$type] ?? $map['info'];
@endphp
<div {{ $attributes->merge(['class' => "rounded-xl border px-4 py-3 text-sm $class"]) }}>
  {{ $slot }}
  </div>

