@props(['type' => 'text'])
@php
    $hasDynamicType = $attributes->has('x-bind:type');
@endphp
<input @unless($hasDynamicType) type="{{ $type }}" @endunless {{ $attributes->class('field') }}>
