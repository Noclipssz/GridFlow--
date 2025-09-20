@props(['class' => ''])
<div {{ $attributes->merge(['class' => "rounded-2xl border border-slate-200 bg-white shadow-sm dark:bg-slate-800 dark:border-slate-700 $class"]) }}>
  {{ $slot }}
</div>

