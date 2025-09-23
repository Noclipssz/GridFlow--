@props(['type' => 'info'])
@php
    $map = [
        'info' => 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-100',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100',
        'danger' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-100',
    ];
    $class = $map[$type] ?? $map['info'];
@endphp
<div {{ $attributes->class(['rounded-xl border px-4 py-3 text-sm', $class]) }}>
    {{ $slot }}
</div>
