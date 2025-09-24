@props([
    'disabled' => false,
    'type' => 'text',
    'id' => null,
    'name' => null,
    'value' => null,
    'required' => false,
    'placeholder' => null,
])

<input
    type="{{ $type }}"
    id="{{ $id ?? $name }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    @if ($required) required @endif
    @if ($disabled) disabled @endif
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'block w-full rounded-xl border-slate-300 dark:bg-slate-800 dark:border-slate-600 focus:border-amber-500 focus:ring-amber-500']) }}
>